const widget = document.querySelector('[data-chatbot-widget]');

if (widget instanceof HTMLElement) {
    const endpoint = widget.dataset.endpoint || '/chatbot/message';
    const launcher = document.querySelector('#nabe-chatbot-launcher');
    const launcherLabel = widget.querySelector('[data-chatbot-launcher-label]');
    const panel = document.querySelector('#nabe-chatbot-panel');
    const closeButton = document.querySelector('#nabe-chatbot-close');
    const messagesBox = document.querySelector('#nabe-chatbot-messages');
    const loadingBox = document.querySelector('#nabe-chatbot-loading');
    const errorBox = document.querySelector('#nabe-chatbot-error');
    const errorText = document.querySelector('#nabe-chatbot-error-text');
    const errorDismiss = document.querySelector('#nabe-chatbot-error-dismiss');
    const branchSelect = document.querySelector('#nabe-chatbot-branch');
    const form = document.querySelector('#nabe-chatbot-form');
    const input = document.querySelector('#nabe-chatbot-input');
    const submitButton = document.querySelector('#nabe-chatbot-submit');
    const quickPromptButtons = widget.querySelectorAll('[data-chatbot-prompt]');

    let isOpen = false;
    let isLoading = false;

    const badgeClasses = {
        Terlaris: 'bg-gold text-gold-ink',
        Promo: 'bg-blush text-blush-ink',
    };

    function prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function scrollMessagesToBottom() {
        if (!messagesBox) return;
        messagesBox.scrollTo({
            top: messagesBox.scrollHeight,
            behavior: prefersReducedMotion() ? 'auto' : 'smooth',
        });
    }

    function showError(message) {
        if (!errorBox || !errorText) return;
        errorText.textContent = message;
        errorBox.classList.remove('hidden');
        errorBox.classList.add('flex');
    }

    function clearError() {
        if (!errorBox || !errorText) return;
        errorText.textContent = '';
        errorBox.classList.add('hidden');
        errorBox.classList.remove('flex');
    }

    function setLoadingState(loading) {
        isLoading = loading;
        if (loadingBox) {
            loadingBox.classList.toggle('hidden', !loading);
            loadingBox.classList.toggle('flex', loading);
        }
        if (submitButton instanceof HTMLButtonElement) {
            submitButton.disabled = loading;
        }
        quickPromptButtons.forEach((button) => {
            if (button instanceof HTMLButtonElement) {
                button.disabled = loading;
            }
        });
    }

    function setOpenState(open, restoreFocus = false) {
        isOpen = open;
        if (!panel || !launcher) return;

        panel.classList.toggle('hidden', !open);
        panel.classList.toggle('flex', open);
        panel.setAttribute('aria-hidden', String(!open));
        launcher.setAttribute('aria-expanded', String(open));

        if (launcherLabel) {
            launcherLabel.textContent = open ? 'Tutup chat' : 'Hai, Nabe di sini!';
        }

        if (open) {
            scrollMessagesToBottom();
            if (input instanceof HTMLElement) {
                input.focus();
            }
        } else if (restoreFocus && launcher instanceof HTMLElement) {
            launcher.focus();
        }
    }

    function appendUserMessage(text) {
        if (!messagesBox) return;

        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble-user self-end ml-auto max-w-[85%] rounded-[5px] bg-primary p-3 text-[12px] leading-relaxed text-white';

        const paragraph = document.createElement('p');
        paragraph.textContent = text;
        bubble.appendChild(paragraph);

        messagesBox.appendChild(bubble);
        scrollMessagesToBottom();
    }

    function appendBotMessage(payload) {
        if (!messagesBox) return;

        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble-bot self-start max-w-[92%] rounded-[5px] border border-line/70 bg-soft p-3 text-[12px] leading-relaxed text-ink space-y-2.5';

        const paragraph = document.createElement('p');
        paragraph.textContent = payload.reply || 'Maaf, belum ada jawaban yang dapat ditampilkan.';
        bubble.appendChild(paragraph);

        if (Array.isArray(payload.items) && payload.items.length > 0) {
            const list = document.createElement('div');
            list.className = 'space-y-2';

            payload.items.slice(0, 3).forEach((item) => {
                const card = document.createElement('div');
                card.className = 'rounded-[5px] border border-line bg-white p-2.5 space-y-1';

                const headerRow = document.createElement('div');
                headerRow.className = 'flex items-start justify-between gap-2';

                const title = document.createElement('strong');
                title.className = 'text-[12px] font-bold text-ink';
                title.textContent = item.title || '';
                headerRow.appendChild(title);

                if (Array.isArray(item.badges) && item.badges.length > 0) {
                    const badgeWrap = document.createElement('div');
                    badgeWrap.className = 'flex flex-wrap gap-1 shrink-0';

                    item.badges.forEach((badgeLabel) => {
                        const badge = document.createElement('span');
                        const colorClass = badgeClasses[badgeLabel] || 'bg-sky text-primary-dark';
                        badge.className = `rounded-[4px] px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider ${colorClass}`;
                        badge.textContent = badgeLabel;
                        badgeWrap.appendChild(badge);
                    });

                    headerRow.appendChild(badgeWrap);
                }

                card.appendChild(headerRow);

                if (item.meta) {
                    const meta = document.createElement('p');
                    meta.className = 'text-[11px] text-muted leading-snug';
                    meta.textContent = item.meta;
                    card.appendChild(meta);
                }

                if (item.url) {
                    const link = document.createElement('a');
                    link.href = item.url;
                    link.className = 'inline-block text-[11px] font-bold text-primary hover:underline';
                    link.textContent = 'Lihat detail ↗';
                    if (String(item.url).startsWith('http')) {
                        link.target = '_blank';
                        link.rel = 'noopener noreferrer';
                    }
                    card.appendChild(link);
                }

                list.appendChild(card);
            });

            bubble.appendChild(list);
        }

        if (Array.isArray(payload.actions) && payload.actions.length > 0) {
            const actionWrap = document.createElement('div');
            actionWrap.className = 'flex flex-wrap gap-2 pt-1';

            payload.actions.forEach((action) => {
                if (!action.url || !action.label) return;
                const actionLink = document.createElement('a');
                actionLink.href = action.url;
                actionLink.className = 'inline-flex items-center gap-1 rounded-[5px] border border-primary/40 bg-white px-2.5 py-1 text-[11px] font-bold text-primary hover:bg-primary hover:text-white transition-colors';
                actionLink.textContent = `${action.label} ↗`;
                if (String(action.url).startsWith('http') && !String(action.url).startsWith(window.location.origin)) {
                    actionLink.target = '_blank';
                    actionLink.rel = 'noopener noreferrer';
                }
                actionWrap.appendChild(actionLink);
            });

            bubble.appendChild(actionWrap);
        }

        messagesBox.appendChild(bubble);
        scrollMessagesToBottom();
    }

    async function sendMessage(rawText) {
        const message = rawText.trim();
        if (isLoading) return;

        if (!message) {
            showError('Tulis pertanyaan terlebih dahulu sebelum mengirim.');
            if (input instanceof HTMLElement) {
                input.focus();
            }
            return;
        }

        clearError();
        appendUserMessage(message);

        if (input instanceof HTMLInputElement) {
            input.value = '';
        }

        setLoadingState(true);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const rawBranch = branchSelect instanceof HTMLSelectElement ? branchSelect.value : '';
        const branchId = rawBranch !== '' ? Number(rawBranch) : null;

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    message,
                    branch_id: Number.isInteger(branchId) ? branchId : null,
                }),
            });

            const data = await response.json().catch(() => null);

            if (!response.ok) {
                const validationMessage = data?.errors?.message?.[0]
                    || data?.message
                    || (response.status === 429
                        ? 'Terlalu banyak permintaan. Tunggu sebentar sebelum mengirim pertanyaan lagi.'
                        : 'Maaf, asisten sedang mengalami kendala memproses pertanyaanmu. Silakan coba lagi.');
                showError(validationMessage);
                return;
            }

            appendBotMessage(data || {});
        } catch {
            showError('Koneksi gagal. Periksa jaringanmu lalu coba kirim pertanyaan lagi.');
        } finally {
            setLoadingState(false);
        }
    }

    launcher?.addEventListener('click', () => {
        setOpenState(!isOpen, isOpen);
    });

    closeButton?.addEventListener('click', () => {
        setOpenState(false, true);
    });

    errorDismiss?.addEventListener('click', clearError);

    quickPromptButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const prompt = button.getAttribute('data-chatbot-prompt') || '';
            if (prompt) {
                sendMessage(prompt);
            }
        });
    });

    form?.addEventListener('submit', (event) => {
        event.preventDefault();
        const text = input instanceof HTMLInputElement ? input.value : '';
        sendMessage(text);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen) {
            setOpenState(false, true);
        }
    });
}
