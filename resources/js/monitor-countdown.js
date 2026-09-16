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
        scans: [], // ✅ NEW: Re-integrated reactivity for logs
        summary: { percentage: 0, present: 0 }, // ✅ NEW: Re-integrated summary

        init() {
            if (this.isClosed) {
                this.startScheduleCheck();
                return;
            }
            this.renderSeconds();
            this.refreshTimer = window.setInterval(() => this.tick(), 1000);
        },

        destroy() {
            window.clearInterval(this.refreshTimer);
            window.clearInterval(this.scheduleCheckTimer);
        },

        tick() {
            if (this.isClosed) return;
            if (this.seconds <= 0) {
                this.refresh();
                return;
            }

            this.seconds -= 1;
        },

        startScheduleCheck() {
            this.scheduleCheckTimer = window.setInterval(() => this.checkSchedule(), 30000);
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
                    window.clearInterval(this.refreshTimer);
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
        lastScanAt: 0,
        pollTimer: null,
        startPoll(url) { this.recentUrl = url; this.pollTimer = setInterval(()=>this.pollScans(), 3000); window.addEventListener('storage', e=>{ if(e.key==='last_scan') this.handleScan(JSON.parse(e.newValue)) }); },
        async pollScans(){
            if(!this.recentUrl) return;
            try{
                const r=await fetch(this.recentUrl,{headers:{Accept:'application/json'},credentials:'same-origin'});
                if(!r.ok) return;
                const j=await r.json();

                // ✅ Update local Alpine state for premium monitor UI
                this.scans = j.scans || [];
                this.summary = j.summary || { percentage: 0, present: 0 };

                const latest=this.scans[0];
                if(latest && latest.id!==this.lastScanAt){
                    this.lastScanAt=latest.id;
                    this.$dispatch('scan-received',{scan:latest});
                    this.speak(`Selamat Datang ${latest.name}, Berhasil.`);
                }
            }catch(e){}
        },
        handleScan(d){ if(!d) return; this.speak(`Selamat Datang ${d.name}, NISN ${d.identifier}, pukul ${d.time}, Berhasil`); },
        speak(t){ if(!('speechSynthesis' in window)) return; speechSynthesis.cancel(); const u=new SpeechSynthesisUtterance(t); u.lang='id-ID'; u.rate=0.95; speechSynthesis.speak(u); },
    };
}
