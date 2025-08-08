<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Havor Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: none;
        }
        .form-control-user {
            border-radius: 10rem;
            padding: 1.5rem 1rem;
        }
        .btn-user {
            border-radius: 10rem;
            padding: 0.75rem 1rem;
        }
        .login-image {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 0.375rem 0 0 0.375rem;
        }
    </style>
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block login-image">
                                <div class="p-5 text-center text-white d-flex flex-column justify-content-center h-100">
                                    <div>
                                        <i class="bi bi-gear-fill" style="font-size: 4rem;"></i>
                                        <h1 class="h2 mt-3">Havor Admin</h1>
                                        <p class="lead">Manage your company profile with ease</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                        <p class="text-muted mb-4">Please sign in to your account</p>
                                    </div>

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li><i class="bi bi-exclamation-triangle"></i> {{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('admin.authenticate') }}">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control form-control-user" id="email"
                                                   name="email" placeholder="Enter Email Address..."
                                                   value="{{ old('email') }}" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control form-control-user" id="password"
                                                   name="password" placeholder="Password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user w-100">
                                            <i class="bi bi-box-arrow-in-right"></i> Login
                                        </button>
                                    </form>

                                    <hr>
                                    <div class="text-center">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <small class="text-muted">
                                                    <strong>Test Credentials:</strong><br>
                                                    <i class="bi bi-person-badge"></i> <strong>admin@havor.com</strong> / <strong>password123</strong><br>
                                                    <i class="bi bi-person"></i> <strong>editor@havor.com</strong> / <strong>password123</strong>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
