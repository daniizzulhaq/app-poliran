<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\JobChat;
use App\Models\JobListing;
use App\Models\Penyaluran;
use App\Services\JobMatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function interest(JobListing $lowongan): RedirectResponse
    {
        return redirect()->route('public.lowongan.show', $lowongan)->withFragment('chat-admin');
    }

    public function index(Request $request): View|JsonResponse
    {
        $chats = JobChat::visibleTo($request->user())
            ->with(['user:id,name', 'jobListing:id,judul', 'latestMessage'])
            ->withCount(['messages as unread_count' => fn ($q) => $q
                ->where('is_admin', ! $request->user()->isSuperAdmin())->whereNull('read_at')])
            ->orderByDesc('last_message_at')->orderByDesc('id')->paginate(20);

        $items = $chats->getCollection()->map(fn (JobChat $chat) => [
            'id' => $chat->id,
            'name' => $request->user()->isSuperAdmin() ? $chat->user->name : 'Admin Utama',
            'job' => $chat->jobListing->judul,
            'preview' => $chat->latestMessage ? mb_strimwidth($chat->latestMessage->body, 0, 120, '…') : 'Mulai percakapan',
            'time' => $chat->last_message_at?->format('d/m/Y H:i'),
            'unread' => $chat->unread_count,
            'url' => route('chat.show', $chat),
        ])->values();

        if ($request->expectsJson()) {
            return response()->json(['chats' => $items])->header('Cache-Control', 'no-store');
        }

        return view('chat.index', compact('chats', 'items'));
    }

    public function start(Request $request, JobListing $lowongan): RedirectResponse
    {
        abort_unless($lowongan->isAktif() && $lowongan->sisaKuota() > 0, 404);

        $chat = DB::transaction(function () use ($request, $lowongan) {
            $chat = JobChat::firstOrCreate([
                'user_id' => $request->user()->id,
                'job_listing_id' => $lowongan->id,
            ], ['last_message_at' => now()]);

            if ($chat->wasRecentlyCreated) {
                $chat->messages()->create([
                    'sender_id' => $request->user()->id,
                    'client_id' => 'initial-interest',
                    'is_admin' => false,
                    'body' => 'Halo Admin, saya tertarik dengan lowongan “'.$lowongan->judul.'”. Mohon bantu periksa kecocokan profil dan kemampuan saya.',
                ]);
            }

            return $chat;
        });

        return redirect()->route('chat.show', $chat);
    }

    public function show(Request $request, JobChat $chat, JobMatcher $matcher): View
    {
        $this->authorize('view', $chat);
        $chat->load(['jobListing.kategori', 'jobListing.skills', 'user.pencariKerja.skills',
            'user.pencariKerja.kategori', 'user.pencariKerja.kecamatan', 'user.pencariKerja.desa']);
        $pencari = $chat->user->pencariKerja;
        $penyaluran = $pencari ? Penyaluran::where('pencari_kerja_id', $pencari->id)
            ->where('job_listing_id', $chat->job_listing_id)->first() : null;
        $messages = $chat->messages()->with('sender:id,name')->latest('id')->limit(50)->get()->reverse()->values();
        $this->markRead($request, $chat, $messages->pluck('id')->all());

        return view('chat.show', [
            'chat' => $chat,
            'pencari' => $pencari,
            'penyaluran' => $penyaluran,
            'adminLapangan' => $request->user()->isSuperAdmin() && $pencari
                ? $pencari->kecamatan->adminLapanganAktif()->get(['users.id', 'users.name']) : collect(),
            'rincian' => $request->user()->isSuperAdmin() && $pencari ? $matcher->skor($chat->jobListing, $pencari) : null,
            'messages' => $messages->map(fn ($message) => $this->messageData($request, $message)),
            'hasOlder' => $messages->isNotEmpty() && $chat->messages()->where('id', '<', $messages->first()->id)->exists(),
        ]);
    }

    public function messages(Request $request, JobChat $chat): JsonResponse
    {
        $this->authorize('view', $chat);
        $data = $request->validate([
            'after' => ['nullable', 'integer', 'min:0'],
            'before' => ['nullable', 'integer', 'min:1', 'prohibits:after'],
        ]);
        $older = isset($data['before']);
        $query = $chat->messages()->with('sender:id,name');
        if ($older) {
            $query->where('id', '<', $data['before'])->orderByDesc('id');
        } else {
            $query->where('id', '>', $data['after'] ?? 0)->orderBy('id');
        }
        $messages = $query->limit(51)->get();
        $hasMore = $messages->count() > 50;
        $messages = $messages->take(50);
        if ($older) {
            $messages = $messages->reverse();
        }
        $this->markRead($request, $chat, $messages->pluck('id')->all());

        return response()->json([
            'messages' => $messages->values()->map(fn ($message) => $this->messageData($request, $message)),
            'has_more' => $hasMore,
        ])->header('Cache-Control', 'no-store');
    }

    public function send(Request $request, JobChat $chat): JsonResponse
    {
        $this->authorize('view', $chat);
        if (is_string($request->input('body'))) {
            $request->merge(['body' => trim($request->input('body'))]);
        }
        $data = $request->validate(['body' => ['required', 'string', 'max:4000'], 'client_id' => ['required', 'uuid']]);

        $message = DB::transaction(function () use ($request, $chat, $data) {
            // Serialize sends in this conversation, keeping message cursors in commit order.
            JobChat::whereKey($chat->id)->lockForUpdate()->firstOrFail();
            $message = $chat->messages()->firstOrCreate(['client_id' => $data['client_id']], [
                'sender_id' => $request->user()->id,
                'is_admin' => $request->user()->isSuperAdmin(),
                'body' => $data['body'],
            ]);
            abort_unless($message->sender_id === $request->user()->id, 409);
            if ($message->wasRecentlyCreated) {
                $chat->update(['last_message_at' => now()]);
            }

            return $message;
        });

        return response()->json(['message' => $this->messageData($request, $message->load('sender:id,name'))], 201);
    }

    public function unread(Request $request): JsonResponse
    {
        $count = ChatMessage::whereHas('jobChat', fn ($q) => $q->visibleTo($request->user()))
            ->where('is_admin', ! $request->user()->isSuperAdmin())->whereNull('read_at')->count();

        return response()->json(['count' => $count])->header('Cache-Control', 'no-store');
    }

    private function markRead(Request $request, JobChat $chat, array $ids): void
    {
        $chat->messages()->whereIn('id', $ids)->where('is_admin', ! $request->user()->isSuperAdmin())
            ->whereNull('read_at')->update(['read_at' => now()]);
    }

    private function messageData(Request $request, ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'sender' => $message->is_admin ? 'Admin Utama · '.$message->sender->name : $message->sender->name,
            'mine' => $message->is_admin === $request->user()->isSuperAdmin(),
            'time' => $message->created_at->format('d/m/Y H:i'),
        ];
    }
}
