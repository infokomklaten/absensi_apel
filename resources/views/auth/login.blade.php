<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Presensi Apel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a56db;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            padding: 24px;
        }

        .login-panel {
            width: 480px;
            max-width: 100%;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px;
            border-radius: 18px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
        }

        .login-header { text-align: center; margin-bottom: 36px; width: 100%; }

        .login-header h2 {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .login-header p { color: #64748b; font-size: 14px; }

        .form-group { margin-bottom: 20px; }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 15px;
        }

        input[type=text], input[type=password] {
            width: 100%;
            padding: 13px 14px 13px 42px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            color: #0f172a;
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(26,86,219,0.08);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            margin-top: 8px;
        }

        .btn-login:hover { background: #1340b0; transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); }

        .error-msg {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .info-box {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 16px;
            margin-top: 28px;
            text-align: center;
        }

        .info-box p { font-size: 12.5px; color: #0369a1; margin: 0; }
        .info-box strong { color: #0c4a6e; }

        @media (max-width: 768px) {
            body { padding: 16px; }
            .login-panel { width: 100%; padding: 36px 24px; }
        }
    </style>
</head>
<body>
    <div class="login-panel">
        <div class="login-header">
            <h2>Selamat Datang</h2>
            <p>Masuk menggunakan NIP dan password Anda</p>
        </div>

        @if($errors->any())
            <div class="error-msg" style="width:100%;">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/login" method="POST" style="width:100%;">
            @csrf

            <div class="form-group">
                <label>NIP</label>
                <div class="input-wrapper">
                    <i class="fas fa-id-badge input-icon"></i>
                    <input type="text" name="nip" placeholder="Masukkan NIP Anda"
                           value="{{ old('nip') }}" autocomplete="username" required>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" placeholder="Masukkan password Anda"
                           autocomplete="current-password" required>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Masuk
            </button>
        </form>

        <div class="info-box" style="width:100%;">
            <p><strong><i class="fas fa-info-circle me-1"></i>Informasi Login</strong></p>
            <p style="margin-top:6px;">Password default = NIP Anda.<br>Hubungi admin jika lupa password.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
