<?php
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportsVani - Cricket Management Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #4a6cf7 0%, #2541b2 100%);
            color: white;
            padding: 100px 0;
        }
        .feature-card {
            transition: transform 0.3s ease;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            background-color: rgba(74, 108, 247, 0.1);
            color: #4a6cf7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .cta {
            background-color: #f8f9fa;
            padding: 80px 0;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 50px 0 20px;
        }
        .footer a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
        }
        .footer a:hover {
            color: white;
        }
        .app-badge {
            height: 40px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                SportsVani
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#download">Download</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/superadmin/login.php" class="btn btn-outline-primary me-2">Admin Login</a>
                    <a href="#download" class="btn btn-primary">Download App</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h1 class="display-4 fw-bold mb-3">SportsVani</h1>
                    <p class="lead mb-4">The ultimate cricket management platform for players, teams, and tournaments</p>
                    <div class="d-flex flex-wrap">
                        <a href="#download" class="btn btn-light btn-lg me-2 mb-2">Download App</a>
                        <a href="/superadmin/login.php" class="btn btn-outline-light btn-lg mb-2">Admin Login</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="assets/images/hero-image.png" alt="Cricket players" class="img-fluid rounded-3 shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" id="features">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Platform Features</h2>
                <p class="text-muted">Everything you need to manage cricket players, teams, and tournaments</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm feature-card h-100">
                        <div class="card-body p-4">
                            <div class="feature-icon">
                                <i class="bi bi-person-badge fs-4"></i>
                            </div>
                            <h4 class="card-title">Player Management</h4>
                            <p class="card-text text-muted">Create detailed player profiles with statistics, match history, and performance analytics.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm feature-card h-100">
                        <div class="card-body p-4">
                            <div class="feature-icon">
                                <i class="bi bi-shield-check fs-4"></i>
                            </div>
                            <h4 class="card-title">Team Management</h4>
                            <p class="card-text text-muted">Create and manage teams, add players, track team statistics, and organize matches.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm feature-card h-100">
                        <div class="card-body p-4">
                            <div class="feature-icon">
                                <i class="bi bi-trophy fs-4"></i>
                            </div>
                            <h4 class="card-title">Tournament Organization</h4>
                            <p class="card-text text-muted">Create tournaments with customizable formats, schedules, and leaderboards.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm feature-card h-100">
                        <div class="card-body p-4">
                            <div class="feature-icon">
                                <i class="bi bi-calendar-event fs-4"></i>
                            </div>
                            <h4 class="card-title">Match Scheduling</h4>
                            <p class="card-text text-muted">Schedule matches, manage officials, and track match details and results.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm feature-card h-100">
                        <div class="card-body p-4">
                            <div class="feature-icon">
                                <i class="bi bi-graph-up fs-4"></i>
                            </div>
                            <h4 class="card-title">Live Scoring</h4>
                            <p class="card-text text-muted">Real-time match scoring with detailed statistics and wagon wheel visualization.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm feature-card h-100">
                        <div class="card-body p-4">
                            <div class="feature-icon">
                                <i class="bi bi-camera-video fs-4"></i>
                            </div>
                            <h4 class="card-title">Live Streaming</h4>
                            <p class="card-text text-muted">Stream matches live on YouTube directly from the platform with integrated APIs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- App Download Section -->
    <section class="bg-light py-5" id="download">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="assets/images/app-mockup.png" alt="SportsVani App" class="img-fluid rounded-3">
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-3">Download the SportsVani App</h2>
                    <p class="text-muted mb-4">Get the SportsVani mobile app to manage your cricket activities on the go. Available for iOS and Android devices.</p>
                    <div class="d-flex flex-wrap">
                        <a href="#" class="me-3 mb-3">
                            <img src="assets/images/app-store-badge.png" alt="Download on App Store" class="app-badge">
                        </a>
                        <a href="#" class="mb-3">

