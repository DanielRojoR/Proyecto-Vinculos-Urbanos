<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="\public\assets\css\main.css">
    <link rel="shortcut icon" href="\public\assets\images\favicon\favicon.ico" type="image/x-icon">
    <title>Donar a la Fundación</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .donation-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .form-container {
            padding: 40px 30px;
        }

        .amount-section {
            margin-bottom: 30px;
        }

        .amount-section h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .amount-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .amount-btn {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            padding: 15px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            font-weight: 600;
            color: #495057;
        }

        .amount-btn:hover {
            border-color: #4CAF50;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.2);
        }

        .amount-btn.selected {
            background: #4CAF50;
            border-color: #4CAF50;
            color: white;
        }

        .custom-amount {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1.1rem;
            transition: border-color 0.3s ease;
        }

        .custom-amount:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .donor-info {
            margin-bottom: 30px;
        }

        .donor-info h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .donate-btn {
            width: 100%;
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            border: none;
            padding: 20px;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .donate-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(76, 175, 80, 0.3);
        }

        .donate-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .loading.show {
            display: block;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4CAF50;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .security-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
            text-align: center;
        }

        .security-info img {
            height: 40px;
            margin: 0 10px;
            opacity: 0.7;
        }

        .security-info p {
            margin-top: 10px;
            color: #666;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .donation-container {
                margin: 10px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .form-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">  
            <div class="navbar-left">
                <a class="btn-navbar-logo" href="..\..\public\index.php">Vinculos Urbanos</a>
            </div>
            <div class="navbar-right">
                <ul>     
                    <li>
                        <a class="btn btn-sm btn-navbar" href="initiatives_view.php">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M640-440 474-602q-31-30-52.5-66.5T400-748q0-55 38.5-93.5T532-880q32 0 60 13.5t48 36.5q20-23 48-36.5t60-13.5q55 0 93.5 38.5T880-748q0 43-21 79.5T807-602L640-440Zm0-112 109-107q19-19 35-40.5t16-48.5q0-22-15-37t-37-15q-14 0-26.5 5.5T700-778l-60 72-60-72q-9-11-21.5-16.5T532-800q-22 0-37 15t-15 37q0 27 16 48.5t35 40.5l109 107ZM280-220l278 76 238-74q-5-9-14.5-15.5T760-240H558q-27 0-43-2t-33-8l-93-31 22-78 81 27q17 5 40 8t68 4q0-11-6.5-21T578-354l-234-86h-64v220ZM40-80v-440h304q7 0 14 1.5t13 3.5l235 87q33 12 53.5 42t20.5 66h80q50 0 85 33t35 87v40L560-60l-280-78v58H40Zm80-80h80v-280h-80v280Zm520-546Z"/></svg>
                            Iniciativas
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-sm btn-navbar" href="financing_view.php">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M444-200h70v-50q50-9 86-39t36-89q0-42-24-77t-96-61q-60-20-83-35t-23-41q0-26 18.5-41t53.5-15q32 0 50 15.5t26 38.5l64-26q-11-35-40.5-61T516-710v-50h-70v50q-50 11-78 44t-28 74q0 47 27.5 76t86.5 50q63 23 87.5 41t24.5 47q0 33-23.5 48.5T486-314q-33 0-58.5-20.5T390-396l-66 26q14 48 43.5 77.5T444-252v52Zm36 120q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                            Financiamiento
                            
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-sm btn-navbar" href="about_us_view.php">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M38-428q-18-36-28-73T0-576q0-112 76-188t188-76q63 0 120 26.5t96 73.5q39-47 96-73.5T696-840q112 0 188 76t76 188q0 38-10 75t-28 73q-11-19-26-34t-35-24q9-23 14-45t5-45q0-78-53-131t-131-53q-81 0-124.5 44.5T480-616q-48-56-91.5-100T264-760q-78 0-131 53T80-576q0 23 5 45t14 45q-20 9-35 24t-26 34ZM0-80v-63q0-44 44.5-70.5T160-240q13 0 25 .5t23 2.5q-14 20-21 43t-7 49v65H0Zm240 0v-65q0-65 66.5-105T480-290q108 0 174 40t66 105v65H240Zm540 0v-65q0-26-6.5-49T754-237q11-2 22.5-2.5t23.5-.5q72 0 116 26.5t44 70.5v63H780ZM480-210q-57 0-102 15t-53 35h311q-9-20-53.5-35T480-210Zm-320-70q-33 0-56.5-23.5T80-360q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T160-280Zm640 0q-33 0-56.5-23.5T720-360q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T800-280Zm-320-40q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-440q0 50-34.5 85T480-320Zm0-160q-17 0-28.5 11.5T440-440q0 17 11.5 28.5T480-400q17 0 28.5-11.5T520-440q0-17-11.5-28.5T480-480Zm0 40Zm1 280Z"/></svg>
                            Quienes Somos
                        </a>
                    </li>
                    <li>
                        <div class="dropdown">
                            <span class="btn btn-sm btn-dono btn-hover">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-120v-80h280v-560H480v-80h280q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H480Zm-80-160-55-58 102-102H120v-80h327L345-622l55-58 200 200-200 200Z"/></svg>
                            </span>
                            <div class="dropdown-content">
                                
                            </div>
                        </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="donation-container">
        <div class="header">
            <h1>💖 Haz una Donación</h1>
            <p>Tu apoyo hace la diferencia en nuestra misión</p>
        </div>

        <div class="form-container">
            <div class="error-message" id="errorMessage"></div>

            <form id="donationForm">
                <div class="amount-section">
                    <h3>Selecciona el monto a donar</h3>
                    <div class="amount-buttons">
                        <div class="amount-btn" data-amount="5000">$5.000</div>
                        <div class="amount-btn" data-amount="10000">$10.000</div>
                        <div class="amount-btn" data-amount="25000">$25.000</div>
                        <div class="amount-btn" data-amount="50000">$50.000</div>
                        <div class="amount-btn" data-amount="100000">$100.000</div>
                        <div class="amount-btn" data-amount="250000">$250.000</div>
                    </div>
                    <input type="number" 
                           class="custom-amount" 
                           id="customAmount" 
                           placeholder="O ingresa otro monto (mínimo $1.000)"
                           min="1000">
                </div>

                <div class="donor-info">
                    <h3>Información del donante (opcional)</h3>
                    <div class="form-group">
                        <label for="donorName">Nombre completo</label>
                        <input type="text" id="donorName" placeholder="Tu nombre completo">
                    </div>
                    <div class="form-group">
                        <label for="donorEmail">Correo electrónico</label>
                        <input type="email" id="donorEmail" placeholder="tu@email.com">
                    </div>
                    <div class="form-group">
                        <label for="donorPhone">Teléfono</label>
                        <input type="tel" id="donorPhone" placeholder="+56 9 1234 5678">
                    </div>
                </div>

                <button type="submit" class="donate-btn" id="donateBtn">
                    🎁 Donar Ahora
                </button>

                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <p>Procesando tu donación...</p>
                </div>
            </form>

            <div class="security-info">
                <div>
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjQwIiB2aWV3Qm94PSIwIDAgMTAwIDQwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjx0ZXh0IHg9IjUwIiB5PSIyMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEyIiBmaWxsPSIjNDQ3N2NlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkb21pbmFudC1iYXNlbGluZT0iY2VudGVyIj5UcmFuc2Jhbms8L3RleHQ+PC9zdmc+" alt="Transbank">
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMjAgNEMxMS4xNjM0IDQgNCA5LjE2MzQgNCAyMEM0IDMwLjgzNjYgMTEuMTYzNCAzNiAyMCAzNkMyOC44MzY2IDM2IDM2IDMwLjgzNjYgMzYgMjBDMzYgOS4xNjM0IDI4LjgzNjYgNCAyMCA0WiIgZmlsbD0iIzRDQUY1MCIvPjxwYXRoIGQ9Ik0xNiAyMEwyMCAyNEwyNiAxNiIgc3Ryb2tlPSJ3aGl0ZSIgc3Ryb2tlLXdpZHRoPSIzIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiLz48L3N2Zz4=" alt="Seguro">
                </div>
                <p>Pago 100% seguro procesado por Transbank Chile</p>
            </div>
        </div>
    </div>

    <script>
        class DonationSystem {
            constructor() {
                this.selectedAmount = 0;
                this.init();
            }

            init() {
                this.bindEvents();
            }

            bindEvents() {
                // Botones de monto predefinido
                document.querySelectorAll('.amount-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        this.selectAmount(e.target);
                    });
                });

                // Input de monto personalizado
                document.getElementById('customAmount').addEventListener('input', (e) => {
                    this.handleCustomAmount(e.target.value);
                });

                // Formulario de donación
                document.getElementById('donationForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.processDonation();
                });
            }

            selectAmount(btn) {
                // Remover selección anterior
                document.querySelectorAll('.amount-btn').forEach(b => {
                    b.classList.remove('selected');
                });

                // Seleccionar nuevo botón
                btn.classList.add('selected');
                this.selectedAmount = parseInt(btn.dataset.amount);

                // Limpiar input personalizado
                document.getElementById('customAmount').value = '';
            }

            handleCustomAmount(value) {
                const amount = parseInt(value);
                
                if (amount >= 1000) {
                    // Remover selección de botones
                    document.querySelectorAll('.amount-btn').forEach(btn => {
                        btn.classList.remove('selected');
                    });
                    this.selectedAmount = amount;
                } else {
                    this.selectedAmount = 0;
                }
            }

            validateDonation() {
                if (this.selectedAmount < 1000) {
                    throw new Error('El monto mínimo de donación es $1.000');
                }

                const email = document.getElementById('donorEmail').value;
                if (email && !this.isValidEmail(email)) {
                    throw new Error('Por favor ingresa un correo electrónico válido');
                }

                return true;
            }

            isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            formatCurrency(amount) {
                return new Intl.NumberFormat('es-CL', {
                    style: 'currency',
                    currency: 'CLP',
                    minimumFractionDigits: 0
                }).format(amount);
            }

            showError(message) {
                const errorDiv = document.getElementById('errorMessage');
                errorDiv.textContent = message;
                errorDiv.classList.add('show');
                
                // Auto-hide después de 5 segundos
                setTimeout(() => {
                    errorDiv.classList.remove('show');
                }, 5000);
            }

            showLoading(show = true) {
                const loading = document.getElementById('loading');
                const btn = document.getElementById('donateBtn');
                
                if (show) {
                    loading.classList.add('show');
                    btn.disabled = true;
                    btn.textContent = 'Procesando...';
                } else {
                    loading.classList.remove('show');
                    btn.disabled = false;
                    btn.textContent = '🎁 Donar Ahora';
                }
            }

            async processDonation() {
                try {
                    // Validar donación
                    this.validateDonation();

                    // Mostrar loading
                    this.showLoading(true);

                    // Preparar datos
                    const donationData = {
                        amount: this.selectedAmount,
                        donor_name: document.getElementById('donorName').value || '',
                        donor_email: document.getElementById('donorEmail').value || '',
                        donor_phone: document.getElementById('donorPhone').value || ''
                    };

                    // Enviar a backend
                    const response = await fetch('/src/Models/donation/create.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(donationData)
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Confirmar antes de redirigir
                        const confirmMessage = `¿Confirmas tu donación de ${this.formatCurrency(this.selectedAmount)}?\n\nSerás redirigido a Transbank para completar el pago.`;
                        
                        if (confirm(confirmMessage)) {
                            // Redirigir a Transbank
                            window.location.href = `${result.url}?token_ws=${result.token}`;
                        } else {
                            this.showLoading(false);
                        }
                    } else {
                        throw new Error(result.error || 'Error al procesar la donación');
                    }

                } catch (error) {
                    this.showError(error.message);
                    this.showLoading(false);
                }
            }
        }

        // Inicializar sistema de donaciones
        document.addEventListener('DOMContentLoaded', () => {
            new DonationSystem();
        });
        <footer class="footer">
        <div class="footer-container">
            <div class="footer-left">
                <h2>Links</h2>
                <ul>
                    <li>
                        <a href="">Preguntas frecuentes</a>
                    </li>
                    <li>
                        <a href="">Términos y condiciones</a>
                    </li>
                    <li>
                        <a href="">Sé voluntari@!</a>
                    </li>
                </ul>

            </div>
            <div class="footer-center">
                <p>Fundación Vínculos Urbanos</p>
                <p>Desarrollado por Daniel R, Camila V, Samuel C</p>

            </div>
        </div>
    </footer>
    </script>
</body>
</html>