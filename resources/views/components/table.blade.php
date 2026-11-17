@props(['paginator' => null])
<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm']) }}>
    <div class="overflow-x-auto">
        <table class="table-base">
            @isset($head)
                <thead><tr>{{ $head }}</tr></thead>
            @endisset
            <tbody class="divide-y divide-slate-100">{{ $slot }}</tbody>
        </table>
    </div>
    @if ($paginator && $paginator->hasPages())
        <div class="border-t border-slate-100 px-3 py-2">{{ $paginator->links() }}</div>
    @endif
</div>
