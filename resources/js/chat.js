import Alpine from 'alpinejs';

const headers = { Accept: 'application/json' };
const sessionError = (error) => [401, 403, 419].includes(error.response?.status);
const uuid = () => '10000000-1000-4000-8000-100000000000'.replace(/[018]/g, (c) =>
    (Number(c) ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> Number(c) / 4).toString(16));

Alpine.data('chatBadge', (url) => ({
    count: 0,
    timer: null,
    busy: false,
    init() {
        this.refresh = this.refresh.bind(this);
        this.refresh();
        this.timer = setInterval(this.refresh, 10000);
        window.addEventListener('chat-read', this.refresh);
    },
    async refresh() {
        if (document.hidden || this.busy) return;
        this.busy = true;
        try {
            const { data } = await window.axios.get(url, { headers, timeout: 15000 });
            this.count = data.count;
        } catch (error) {
            if (sessionError(error)) clearInterval(this.timer);
        } finally {
            this.busy = false;
        }
    },
    destroy() {
        clearInterval(this.timer);
        window.removeEventListener('chat-read', this.refresh);
    },
}));

Alpine.data('chatInbox', (items, url) => ({
    items,
    error: '',
    busy: false,
    timer: null,
    init() { this.timer = setInterval(() => this.refresh(), 5000); },
    async refresh() {
        if (document.hidden || this.busy) return;
        this.busy = true;
        try {
            const { data } = await window.axios.get(url, { headers, timeout: 15000 });
            this.items = data.chats;
            this.error = '';
        } catch (error) {
            this.error = sessionError(error) ? 'Sesi berakhir atau akses berubah. Silakan masuk kembali.' : 'Koneksi terputus. Daftar chat akan dicoba kembali otomatis.';
            if (sessionError(error)) clearInterval(this.timer);
        } finally {
            this.busy = false;
        }
    },
    destroy() { clearInterval(this.timer); },
}));

Alpine.data('jobChat', (messages, messagesUrl, sendUrl, hasOlder) => ({
    messages,
    hasOlder,
    body: '',
    error: '',
    sendError: '',
    sending: false,
    polling: false,
    loadingOlder: false,
    newMessages: false,
    atBottom: true,
    cursor: messages.at(-1)?.id ?? 0,
    pending: null,
    timer: null,
    init() {
        this.$nextTick(() => this.scrollBottom());
        this.timer = setInterval(() => this.poll(), 3000);
        window.dispatchEvent(new Event('chat-read'));
    },
    merge(incoming) {
        const byId = new Map(this.messages.map((message) => [message.id, message]));
        incoming.forEach((message) => byId.set(message.id, message));
        this.messages = [...byId.values()].sort((a, b) => a.id - b.id);
    },
    checkBottom() {
        const box = this.$refs.messages;
        this.atBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 80;
        if (this.atBottom) this.newMessages = false;
    },
    scrollBottom() {
        this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight;
        this.atBottom = true;
        this.newMessages = false;
    },
    async poll() {
        if (document.hidden || this.polling) return;
        this.polling = true;
        try {
            const { data } = await window.axios.get(messagesUrl, { headers, params: { after: this.cursor }, timeout: 15000 });
            if (data.messages.length) {
                const stick = this.atBottom;
                this.merge(data.messages);
                // Sending a message never advances this cursor: replies in flight must still be fetched.
                this.cursor = data.messages.at(-1).id;
                if (stick) this.$nextTick(() => this.scrollBottom());
                else this.newMessages = true;
            }
            if (!this.sending) this.error = '';
            window.dispatchEvent(new Event('chat-read'));
        } catch (error) {
            this.error = sessionError(error) ? 'Sesi berakhir atau akses berubah. Silakan masuk kembali.' : 'Koneksi terputus. Pesan baru akan dicek kembali otomatis.';
            if (sessionError(error)) clearInterval(this.timer);
        } finally {
            this.polling = false;
        }
    },
    async older() {
        if (this.loadingOlder || !this.hasOlder) return;
        this.loadingOlder = true;
        const box = this.$refs.messages;
        const previousHeight = box.scrollHeight;
        const previousTop = box.scrollTop;
        try {
            const { data } = await window.axios.get(messagesUrl, { headers, params: { before: this.messages[0].id }, timeout: 15000 });
            this.merge(data.messages);
            this.hasOlder = data.has_more;
            this.$nextTick(() => { box.scrollTop = previousTop + box.scrollHeight - previousHeight; });
        } catch {
            this.error = 'Pesan sebelumnya belum dapat dimuat. Silakan coba lagi.';
        } finally {
            this.loadingOlder = false;
        }
    },
    async send() {
        const body = this.body.trim();
        if (!body || this.sending) return;
        if (!this.pending || this.pending.body !== body) this.pending = { body, client_id: uuid() };
        this.sending = true;
        this.sendError = '';
        try {
            const { data } = await window.axios.post(sendUrl, this.pending, { headers, timeout: 15000 });
            this.merge([data.message]);
            this.body = '';
            this.pending = null;
            this.$nextTick(() => this.scrollBottom());
            await this.poll();
        } catch (error) {
            this.sendError = sessionError(error) ? 'Sesi berakhir atau akses berubah. Salin pesan Anda lalu masuk kembali.'
                : error.response?.status === 429 ? 'Terlalu banyak pesan. Tunggu sebentar lalu coba kirim lagi.'
                : error.response?.data?.errors?.body?.[0] ?? 'Pesan belum terkonfirmasi. Klik Kirim Pesan lagi untuk mencoba kembali.';
        } finally {
            this.sending = false;
        }
    },
    destroy() { clearInterval(this.timer); },
}));
