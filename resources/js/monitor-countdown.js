export function monitorCountdown({ initial = {}, refreshUrl = '', isClosed = false, label = '' } = {}) {
    return {
        seconds: Math.floor(Number(initial?.countdown_seconds ?? 15)),
        ttl: 15,
        qr: initial || {},
        refreshUrl,
        refreshing: false,
        refreshTimer: null,
        scheduleCheckTimer: null,
        isClosed: isClosed,
        label: label,
        scans: [],
        summary: { percentage: '0%', present: 0 },

        init() {
            if (this.isClosed) {
                this.scans = [];
                this.summary = { percentage: '0%', present: 0 };
                this.startScheduleCheck();
                return;
            }
            this.renderSeconds();
            this.refreshTimer = window.setInterval(() => this.tick(), 1000);
        },

        destroy() {
            window.clearInterval(this.refreshTimer);
            window.clearInterval(this.scheduleCheckTimer);
            window.clearInterval(this.pollTimer);
            if ('speechSynthesis' in window) speechSynthesis.cancel();
        },

        tick() {
            if (this.isClosed) {
                this.scans = [];
                if ('speechSynthesis' in window) speechSynthesis.cancel();
                return;
            }
            if (this.seconds <= 0) {
                this.refresh();
                return;
            }

            this.seconds -= 1;
        },

        startScheduleCheck() {
            this.scheduleCheckTimer = window.setInterval(() => this.checkSchedule(), 20000);
        },

        async checkSchedule() {
            if (!this.refreshUrl) return;
            try {
                const response = await fetch(this.refreshUrl, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });
                if (!response.ok) return;
                const payload = await response.json();

                if (!payload.is_closed && payload.qr) {
                    window.clearInterval(this.scheduleCheckTimer);
                    this.isClosed = false;
                    this.qr = payload.qr;
                    this.seconds = Math.floor(Number(payload.qr.countdown_seconds ?? this.ttl));
                    this.renderSeconds();
                    this.refreshTimer = window.setInterval(() => this.tick(), 1000);
                    this.pollScans(true);
                } else {
                    this.scans = [];
                    if ('speechSynthesis' in window) speechSynthesis.cancel();
                }
            } catch (e) { /* ignore */ }
        },

        async refresh() {
            if (this.refreshing || ! this.refreshUrl) {
                return;
            }

            this.refreshing = true;

            try {
                const response = await fetch(this.refreshUrl, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });

                if (! response.ok) {
                    throw new Error('QR belum dapat diperbarui.');
                }

                const payload = await response.json();

                if (payload.is_closed) {
                    this.isClosed = true;
                    this.scans = [];
                    window.clearInterval(this.refreshTimer);
                    if ('speechSynthesis' in window) speechSynthesis.cancel();
                    this.startScheduleCheck();
                    return;
                }

                this.qr = payload.qr;
                this.seconds = Math.floor(Number(payload.qr.countdown_seconds ?? this.ttl));
                this.ttl = Math.floor(Number(payload.qr.countdown_seconds ?? 15)) || 15;
            } catch (error) {
                this.$dispatch('monitor-error', { message: error.message });
                this.seconds = this.ttl;
            } finally {
                this.refreshing = false;
            }
        },

        renderSeconds() {
            return String(Math.max(0, this.seconds)).padStart(2, '0');
        },

        progress() {
            return `${Math.max(0, Math.min(100, (this.seconds / this.ttl) * 100))}%`;
        },

        // poll recent scans + voice
        recentUrl: '',
        lastScanAt: -1,
        pollTimer: null,
        startPoll(url) {
            this.recentUrl = url;
            this.pollTimer = setInterval(()=>this.pollScans(), 3000);
            this.pollScans(true);
        },
        async pollScans(silent = false){
            if(!this.recentUrl) return;
            if (this.isClosed) {
                this.scans = [];
                return;
            }

            try{
                const r=await fetch(this.recentUrl,{headers:{Accept:'application/json'},credentials:'same-origin'});
                if(!r.ok) return;
                const j=await r.json();

                this.scans = j.scans || [];
                this.summary = j.summary || { percentage: '0%', present: 0 };

                const latest=this.scans[0];
                if(latest){
                    if (this.lastScanAt === -1) {
                        this.lastScanAt = latest.id;
                    } else if (latest.id !== this.lastScanAt) {
                        this.lastScanAt = latest.id;
                        if (!silent) {
                            this.speak(`Selamat Datang ${latest.name}, Berhasil.`);
                        }
                    }
                }
            }catch(e){}
        },
        speak(t){
            if(!('speechSynthesis' in window) || this.isClosed) return;
            speechSynthesis.cancel();
            const u = new SpeechSynthesisUtterance(t);
            u.lang = 'id-ID';
            u.rate = 0.95;
            speechSynthesis.speak(u);
        },
    };
}
