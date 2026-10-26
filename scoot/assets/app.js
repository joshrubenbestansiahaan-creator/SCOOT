/**
 * SCOOT Customer PWA - Client Script
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ==========================================================================
       1. ONBOARDING / WELCOME SLIDES
       ========================================================================== */
    const nextBtn = document.getElementById('next');
    if (nextBtn) {
        const slides = [...document.querySelectorAll('.slide')];
        const dots = [...document.querySelectorAll('.dots i')];
        let currentIndex = 0;

        nextBtn.addEventListener('click', () => {
            if (currentIndex === slides.length - 1) {
                location.href = '?p=signin';
                return;
            }

            slides[currentIndex].classList.remove('on');
            dots[currentIndex].classList.remove('on');
            
            currentIndex++;
            
            slides[currentIndex].classList.add('on');
            dots[currentIndex].classList.add('on');

            if (currentIndex === slides.length - 1) {
                nextBtn.textContent = 'Start';
            }
        });
    }


    /* ==========================================================================
       2. LEAFLET MAP & SCOOTER PINS
       ========================================================================== */
    const mapEl = document.getElementById('map');
    if (mapEl) {
        try {
            if (typeof L === 'undefined') {
                throw new Error('Leaflet library is not loaded');
            }

            const map = L.map(mapEl, { zoomControl: false }).setView([-6.3612, 106.8252], 17);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            const pinIcon = L.divIcon({
                className: 'pin',
                html: '🛴',
                iconSize: [34, 34]
            });
            
            const cardEl = document.getElementById('card');

            if (typeof SCOOTERS !== 'undefined' && Array.isArray(SCOOTERS)) {
                SCOOTERS.forEach(scooter => {
                    L.marker([scooter.lat, scooter.lng], { icon: pinIcon })
                        .addTo(map)
                        .on('click', () => {
                            if (cardEl) {
                                cardEl.hidden = false;
                                cardEl.innerHTML = `
                                    <div>
                                        <small>Scoot Code</small>
                                        <b>${scooter.code}</b>
                                    </div>
                                    <span>${scooter.battery}%</span>
                                    <form method="post">
                                        <input type="hidden" name="a" value="reserve">
                                        <input type="hidden" name="code" value="${scooter.code}">
                                        <button class="btn sm">Book Now!</button>
                                    </form>
                                `;
                            }
                        });
                });

                if (SCOOTERS.length > 0) {
                    map.fitBounds(
                        SCOOTERS.map(s => [s.lat, s.lng]), 
                        { padding: [60, 60], maxZoom: 18 }
                    );
                }
            }

            setTimeout(() => map.invalidateSize(), 400);
            window.addEventListener('resize', () => map.invalidateSize());

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(pos => {
                    L.circleMarker([pos.coords.latitude, pos.coords.longitude], {
                        radius: 8,
                        color: '#ffffff',
                        weight: 3,
                        fillColor: '#1a73e8',
                        fillOpacity: 1
                    }).addTo(map);
                });
            }
        } catch (err) {
            mapEl.innerHTML = `<p style="padding: 24px;">Map error: ${err.message}</p>`;
        }
    }


    /* ==========================================================================
       3. RIDE TIMER & REAL-TIME COST COUNTER
       ========================================================================== */
    const timerEl = document.getElementById('t');
    if (timerEl) {
        const startTime = Number(timerEl.dataset.start);
        const costEl = document.getElementById('cost');

        const updateTimer = () => {
            const elapsedSeconds = Math.max(0, Math.floor(Date.now() / 1000 - startTime));
            const durationMinutes = Math.max(1, Math.ceil(elapsedSeconds / 60));

            const minutes = String(Math.floor(elapsedSeconds / 60)).padStart(2, '0');
            const seconds = String(elapsedSeconds % 60).padStart(2, '0');
            timerEl.textContent = `${minutes}:${seconds}`;

            if (costEl && typeof U !== 'undefined' && typeof M !== 'undefined') {
                const totalCost = U + (M * durationMinutes);
                costEl.textContent = totalCost.toLocaleString('en-US');
            }
        };

        updateTimer();
        setInterval(updateTimer, 1000);
    }


    /* ==========================================================================
       4. QR CODE CAMERA SCANNER
       ========================================================================== */
    const cameraEl = document.getElementById('cam');
    if (cameraEl) {
        const formEl = document.getElementById('f');
        const statusEl = document.getElementById('st');
        let isScanned = false;

        const parseCode = (rawText) => {
            try {
                rawText = new URL(rawText).searchParams.get('code') || rawText.split('/').filter(Boolean).pop();
            } catch (e) {}
            return rawText.trim().toUpperCase();
        };

        const handleSuccess = (codeText) => {
            if (isScanned) return;
            isScanned = true;

            if (formEl) {
                formEl.code.value = parseCode(codeText);
                if (statusEl) statusEl.textContent = 'Unlocking...';
                formEl.submit();
            }
        };

        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(async stream => {
                cameraEl.srcObject = stream;
                await cameraEl.play();

                const barcodeDetector = ('BarcodeDetector' in window) 
                    ? new BarcodeDetector({ formats: ['qr_code'] }) 
                    : null;

                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d', { willReadFrequently: true });

                const scanLoop = async () => {
                    if (isScanned) return;

                    if (cameraEl.readyState >= 2) {
                        try {
                            if (barcodeDetector) {
                                const barcodes = await barcodeDetector.detect(cameraEl);
                                if (barcodes[0]) return handleSuccess(barcodes[0].rawValue);
                            } else if (typeof jsQR !== 'undefined') {
                                canvas.width = cameraEl.videoWidth;
                                canvas.height = cameraEl.videoHeight;
                                ctx.drawImage(cameraEl, 0, 0);

                                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                                const result = jsQR(imageData.data, imageData.width, imageData.height);
                                if (result) return handleSuccess(result.data);
                            }
                        } catch (err) {}
                    }
                    requestAnimationFrame(scanLoop);
                };

                scanLoop();

                window.addEventListener('pagehide', () => {
                    stream.getTracks().forEach(track => track.stop());
                });
            })
            .catch(() => {
                if (statusEl) statusEl.textContent = 'Camera blocked. Type code below.';
            });
    }


    /* ==========================================================================
       5. RESERVATION HOLD COUNTDOWN
       ========================================================================== */
    const holdEl = document.getElementById('hold');
    if (holdEl) {
        const secondsLeft = Number(holdEl.dataset.left) || 0;
        const endTime = Date.now() + (secondsLeft * 1000);

        const updateCountdown = () => {
            const remaining = Math.max(0, Math.round((endTime - Date.now()) / 1000));
            const minutes = Math.floor(remaining / 60);
            const seconds = String(remaining % 60).padStart(2, '0');

            holdEl.textContent = `${minutes}:${seconds}`;

            if (remaining === 0) {
                setTimeout(() => location.reload(), 1500);
            }
        };

        updateCountdown();
        setInterval(updateCountdown, 1000);
    }


    /* ==========================================================================
       6. THEME SWITCHER (LIGHT / DARK / NIGHT / AUTO)
       ========================================================================== */
    const applyTheme = (themeName) => {
        const resolvedTheme = (themeName === 'auto')
            ? (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
            : themeName;

        document.documentElement.dataset.theme = resolvedTheme;

        const metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (metaThemeColor) {
            const themeColors = {
                light: '#B8E5C8',
                dark: '#121212',
                night: '#1a1410'
            };
            metaThemeColor.content = themeColors[resolvedTheme] || '#B8E5C8';
        }
    };

    const currentTheme = localStorage.getItem('scoot_theme') || 'light';
    applyTheme(currentTheme);

    const themeButtons = [...document.querySelectorAll('[data-t]')];
    themeButtons.forEach(btn => {
        btn.classList.toggle('on', btn.dataset.t === currentTheme);

        btn.addEventListener('click', () => {
            const selectedTheme = btn.dataset.t;
            localStorage.setItem('scoot_theme', selectedTheme);
            applyTheme(selectedTheme);

            themeButtons.forEach(b => b.classList.toggle('on', b === btn));
        });
    });


    /* ==========================================================================
       7. PAGE TRANSITIONS & SWIPE ANIMATIONS
       ========================================================================== */
    const pageOrder = ['home', 'payment', 'scan', 'inbox', 'profile'];
    const currentParam = new URLSearchParams(location.search).get('p') || 'splash';
    const phoneContainer = document.querySelector('.phone');
    const previousParam = sessionStorage.getItem('scoot_prev');

    if (phoneContainer) {
        if (previousParam && pageOrder.includes(currentParam) && pageOrder.includes(previousParam) && previousParam !== currentParam) {
            const isForward = pageOrder.indexOf(currentParam) > pageOrder.indexOf(previousParam);
            phoneContainer.classList.add(isForward ? 'in-r' : 'in-l');
        }

        sessionStorage.setItem('scoot_prev', currentParam);

        document.addEventListener('click', (e) => {
            const linkAnchor = e.target.closest('a[href^="?p="]');
            if (!linkAnchor || e.metaKey || e.ctrlKey) return;

            e.preventDefault();
            const targetParam = new URLSearchParams(linkAnchor.search).get('p');
            const isForward = pageOrder.indexOf(targetParam) > pageOrder.indexOf(currentParam);

            phoneContainer.style.setProperty('--ox', isForward ? '-24px' : '24px');
            phoneContainer.classList.add('leave');

            setTimeout(() => {
                location.href = linkAnchor.href;
            }, 170);
        });

        window.addEventListener('pageshow', () => {
            phoneContainer.classList.remove('leave');
        });
    }


    /* ==========================================================================
       8. LIVE CHAT & AUTO-SCROLL TO BOTTOM
       ========================================================================== */
    const msgsContainer = document.getElementById('msgs');
    if (msgsContainer) {
        const chatForm = document.getElementById('chatf');
        let lastMsgId = Number(msgsContainer.dataset.last) || 0;

        // Helper fungsi auto scroll ke pesan paling bawah
        const scrollToBottom = () => {
            msgsContainer.scrollTop = msgsContainer.scrollHeight;
        };

        const renderMessage = (msgObj) => {
            const bubble = document.createElement('div');
            bubble.className = `m ${Number(msgObj.admin) ? 'a' : 'u'}`;
            bubble.dataset.id = msgObj.id;
            bubble.textContent = msgObj.body;

            msgsContainer.appendChild(bubble);
            lastMsgId = Math.max(lastMsgId, Number(msgObj.id));

            scrollToBottom();
        };

        const pollMessages = () => {
            fetch(`?p=poll&last=${lastMsgId}`)
                .then(res => res.json())
                .then(messagesList => {
                    if (Array.isArray(messagesList)) {
                        messagesList.forEach(renderMessage);
                    }
                })
                .catch(() => {});
        };

        // Auto polling pesan baru setiap 4 detik
        setInterval(pollMessages, 4000);

        // Submit form pesan tanpa reload halaman
        if (chatForm) {
            chatForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const textInput = chatForm.querySelector('input[name="body"]');
                if (!textInput || !textInput.value.trim()) return;

                const formData = new FormData(chatForm);
                textInput.value = '';

                fetch('', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'fetch' }
                }).then(pollMessages);
            });
        }

        // Auto scroll ke paling bawah saat pertama kali membuka layar Inbox
        scrollToBottom();
        const msgs = document.getElementById('msgs');
        if (msgs) {
            // Scroll instan ke paling bawah
            msgs.scrollTop = msgs.scrollHeight;
        }
    }


    /* ==========================================================================
       9. SERVICE WORKER REGISTRATION (PWA)
       ========================================================================== */
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('sw.js').catch(() => {});
        });
    }

});