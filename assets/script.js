// ========== AUTO-FORMATAR CARTAO ==========
document.addEventListener('DOMContentLoaded', function () {
    const cartaoInput = document.getElementById('numero_cartao');
    if (cartaoInput) {
        cartaoInput.addEventListener('input', function (e) {
            let val = this.value.replace(/\D/g, '').substring(0, 16);
            let formatted = val.replace(/(.{4})/g, '$1 ').trim();
            this.value = formatted;
        });

        cartaoInput.addEventListener('blur', function () {
            let val = this.value.replace(/\s/g, '');
            if (val.length > 0 && val.length < 16) {
                this.setCustomValidity('O cartão deve ter 16 dígitos');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // ========== AUTO-FORMATAR PIN ==========
    const pinInput = document.getElementById('pin');
    if (pinInput) {
        pinInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').substring(0, 4);
        });
    }

    // ========== AUTO-FORMATAR VALOR MONETARIO ==========
    const valorInputs = document.querySelectorAll('input[inputmode="decimal"]');
    valorInputs.forEach(function (input) {
        input.addEventListener('input', function () {
            let val = this.value.replace(',', '.');
            if (val.includes('.')) {
                let parts = val.split('.');
                if (parts[1] && parts[1].length > 2) {
                    parts[1] = parts[1].substring(0, 2);
                    this.value = parts.join('.');
                }
            }
        });
    });

    // ========== CONFIRMACAO ==========
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    // ========== MENSAGENS AUTO-SUMMIR ==========
    document.querySelectorAll('.alert-success, .alert-danger, .atm-success, .atm-error').forEach(function (el) {
        if (el.classList.contains('alert-danger') || el.classList.contains('atm-error')) return;
        setTimeout(function () {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(function () { el.style.display = 'none'; }, 500);
        }, 5000);
    });
});

// ========== TECLADO FÍSICO ATM ==========
    const teclado = document.querySelector('.atm-keypad');
    if (teclado) {
        teclado.addEventListener('click', function (e) {
            const key = e.target.closest('.atm-key');
            if (!key) return;

            const active = document.activeElement;
            const isInput = active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA');

            const texto = key.textContent.trim();

            // OK - submeter formulário
            if (texto === 'OK') {
                const form = document.querySelector('.atm-form');
                if (form) form.submit();
                return;
            }

            // Anular - voltar atrás
            if (texto === 'Anular') {
                const backLink = document.querySelector('.atm-actions a:first-child, .atm-btn-secondary');
                if (backLink) { window.location.href = backLink.href; }
                else { window.history.back(); }
                return;
            }

            // Limpar - limpar campo
            if (texto === 'Limpar') {
                if (isInput) {
                    active.value = '';
                    active.focus();
                }
                return;
            }

            // Corrigir - apagar último caracter
            if (texto === 'Corrigir') {
                if (isInput) {
                    const start = active.selectionStart;
                    if (start > 0) {
                        active.value = active.value.slice(0, start - 1) + active.value.slice(active.selectionEnd);
                        active.setSelectionRange(start - 1, start - 1);
                    }
                    active.focus();
                }
                return;
            }

            // Números - inserir no campo focado
            if (/^[0-9]$/.test(texto)) {
                if (isInput) {
                    const start = active.selectionStart;
                    const end = active.selectionEnd;
                    active.value = active.value.slice(0, start) + texto + active.value.slice(end);
                    active.setSelectionRange(start + 1, start + 1);
                    active.focus();
                    // Disparar evento input para formatadores
                    active.dispatchEvent(new Event('input', { bubbles: true }));
                } else {
                    // Se nenhum input focado, focar o primeiro input do formulário
                    const firstInput = document.querySelector('.atm-form input');
                    if (firstInput && !firstInput.value) {
                        firstInput.value = texto;
                        firstInput.focus();
                        firstInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
                return;
            }

            // ± ou . - inserir no campo focado
            if (texto === '±' || texto === '.') {
                if (isInput) {
                    const start = active.selectionStart;
                    const end = active.selectionEnd;
                    active.value = active.value.slice(0, start) + '.' + active.value.slice(end);
                    active.setSelectionRange(start + 1, start + 1);
                    active.focus();
                    active.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
        });
    }

// ========== IMPRIMIR COMPROVATIVO ==========
function imprimirComprovativo() {
    const receipt = document.getElementById('comprovativo');
    if (!receipt) return;
    const win = window.open('', '_blank', 'width=400,height=600');
    win.document.write('<html><head><title>Comprovativo</title>');
    win.document.write('<style>body{font-family:"Courier New",monospace;font-size:14px;padding:40px;text-align:center;}');
    win.document.write('h2{margin-bottom:20px;} .linha{border-top:1px dashed #000;margin:15px 0;}');
    win.document.write('.valor{font-size:24px;font-weight:bold;margin:10px 0;}');
    win.document.write('.codigo{letter-spacing:3px;font-size:18px;margin:15px 0;}');
    win.document.write('@media print{body{padding:20px;}}');
    win.document.write('</style></head><body>');
    win.document.write(receipt.innerHTML);
    win.document.write('<br><button onclick="window.print()">Imprimir</button>');
    win.document.write('</body></html>');
    win.document.close();
}
