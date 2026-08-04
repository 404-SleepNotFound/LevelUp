<nav class="navbar navbar-expand-lg sticky-top lu-navbar py-2">
    <div class="container-fluid px-3 px-lg-4">
        
        <!-- 1. Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="/index.php">
            <div class="lu-logo-icon">
                <i class="bi bi-rocket-takeoff-fill fs-6"></i>
            </div>
            <span class="fs-5 fw-bold text-white tracking-tight">Level<span style="color: var(--color-primary);">Up</span></span>
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler border-0 shadow-none text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <i class="bi bi-list fs-2 text-white"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            
            <!-- 2. Navigation Menu -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1 ms-lg-4">
                <li class="nav-item"><a class="nav-link lu-nav-link" href="/index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link lu-nav-link" href="/courses.php">Courses</a></li>
                <li class="nav-item"><a class="nav-link lu-nav-link" href="/learning-path.php">Learning Path</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link lu-nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Labs</a>
                    <ul class="dropdown-menu lu-dropdown-menu border-0 shadow-lg mt-2">
                        <li><a class="dropdown-item lu-dropdown-item py-2" href="/coding-lab.php"><i class="bi bi-code-square me-2 text-primary"></i>Coding Lab</a></li>
                        <li><a class="dropdown-item lu-dropdown-item py-2" href="/virtual-lab.php"><i class="bi bi-pc-display me-2 text-info"></i>Virtual Lab</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link lu-nav-link d-flex align-items-center gap-1" href="/ai-chat.php"><i class="bi bi-robot text-primary"></i> AI Tutor</a></li>
            </ul>

            <!-- Right Side Tools -->
            <div class="d-flex align-items-center gap-3 flex-wrap flex-lg-nowrap mt-3 mt-lg-0">
                
                <!-- 3. Search Bar -->
                <div class="lu-search-wrapper">
                    <i class="bi bi-search lu-search-icon"></i>
                    <input type="text" class="form-control lu-search-input shadow-none" id="globalSearchInput" placeholder="Search courses, skills...">
                    <kbd class="lu-search-shortcut d-none d-md-block">Ctrl K</kbd>
                </div>

                <?php if ($isLoggedIn): ?>
                    <!-- 7. Daily Streak & 6. XP/Level -->
                    <div class="d-none d-xl-flex align-items-center gap-2 px-3 py-1 rounded-pill" style="background: var(--bg-surface);">
                        <div class="d-flex align-items-center gap-1 lu-badge-streak px-2 py-1 rounded-pill fs-7 fw-bold" title="Daily Streak">
                            <i class="bi bi-fire"></i> <?php echo htmlspecialchars($user['streak']); ?>
                        </div>
                        <div class="d-flex align-items-center gap-1 lu-badge-xp px-2 py-1 rounded-pill fs-7 fw-bold" title="<?php echo htmlspecialchars($user['xp']); ?> XP">
                            <i class="bi bi-star-fill"></i> Lv.<?php echo htmlspecialchars($user['level']); ?>
                        </div>
                    </div>

                    <!-- 4. AI Quick Access -->
                    <button class="btn lu-btn-ai rounded-pill px-3 py-1 fw-bold fs-7 d-flex align-items-center gap-2 shadow-none" data-bs-toggle="modal" data-bs-target="#aiQuickModal">
                        <i class="bi bi-stars"></i> <span class="d-none d-sm-inline">Ask AI</span>
                    </button>

                    <!-- 5. Notifications -->
                    <div class="dropdown">
                        <button class="btn border-0 shadow-none position-relative text-secondary p-1" data-bs-toggle="dropdown" id="notifButton">
                            <i class="bi bi-bell fs-5 text-white"></i>
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-2 border-dark rounded-circle" style="margin-top: 5px; margin-left: -5px;">
                                <span class="visually-hidden">New alerts</span>
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end lu-dropdown-menu p-0 mt-3 shadow-lg" style="width: 320px;">
                            <div class="p-3 border-bottom" style="border-color: var(--border-color) !important;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-white">Notifications</h6>
                                    <span class="badge bg-primary rounded-pill">2 New</span>
                                </div>
                            </div>
                            <div class="list-group list-group-flush" id="notifList">
                                <!-- AJAX Content loads here -->
                                <div class="text-center p-4">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                </div>
                            </div>
                            <div class="p-2 text-center border-top" style="border-color: var(--border-color) !important;">
                                <a href="/notifications.php" class="text-decoration-none text-primary fs-7">View All</a>
                            </div>
                        </div>
                    </div>

                    <!-- 8. Theme Switch & 9. Language -->
                    <div class="dropdown">
                        <button class="btn border-0 shadow-none text-secondary p-1" data-bs-toggle="dropdown">
                            <i class="bi bi-globe fs-5 text-white"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end lu-dropdown-menu mt-3 shadow-lg fs-7">
                            <li><h6 class="dropdown-header text-uppercase">Language</h6></li>
                            <li><a class="dropdown-item lu-dropdown-item active" href="#">English</a></li>
                            <li><a class="dropdown-item lu-dropdown-item" href="#">ภาษาไทย</a></li>
                            <li><hr class="dropdown-divider" style="border-color: var(--border-color);"></li>
                            <li><h6 class="dropdown-header text-uppercase">Theme</h6></li>
                            <li><a class="dropdown-item lu-dropdown-item theme-toggle" href="#" data-theme-value="light"><i class="bi bi-sun me-2"></i>Light</a></li>
                            <li><a class="dropdown-item lu-dropdown-item theme-toggle" href="#" data-theme-value="dark"><i class="bi bi-moon-stars me-2"></i>Dark</a></li>
                            <li><a class="dropdown-item lu-dropdown-item theme-toggle" href="#" data-theme-value="auto"><i class="bi bi-display me-2"></i>Auto</a></li>
                        </ul>
                    </div>

                    <!-- 10. User Menu -->
                    <div class="dropdown ms-2">
                        <button class="btn border-0 shadow-none p-0" data-bs-toggle="dropdown">
                            <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Profile" class="rounded-circle border border-2 border-secondary" width="38" height="38">
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end lu-dropdown-menu mt-3 shadow-lg pb-2">
                            <li class="px-3 py-2 border-bottom mb-2" style="border-color: var(--border-color) !important;">
                                <div class="fw-bold text-white"><?php echo htmlspecialchars($user['name']); ?></div>
                                <div class="text-secondary fs-7"><?php echo htmlspecialchars($user['email']); ?></div>
                            </li>
                            <li><a class="dropdown-item lu-dropdown-item py-2" href="/dashboard.php"><i class="bi bi-grid-1x2 me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item lu-dropdown-item py-2" href="/profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item lu-dropdown-item py-2" href="/settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider" style="border-color: var(--border-color);"></li>
                            <li><a class="dropdown-item lu-dropdown-item py-2 text-danger" href="/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>

                <?php else: ?>
                    <!-- Guest Menu -->
                    <a href="/login.php" class="btn text-white fw-medium px-3 shadow-none">Login</a>
                    <a href="/register.php" class="btn btn-primary rounded-pill px-4 fw-medium shadow-none">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Mock AI Quick Modal (Required by UI) -->
<div class="modal fade" id="aiQuickModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: var(--bg-surface); border: 1px solid var(--border-color);">
      <div class="modal-header border-bottom-0">
        <h5 class="modal-title text-white"><i class="bi bi-stars text-primary"></i> Ask AI Tutor</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body pb-4">
        <textarea class="form-control mb-3" rows="3" placeholder="Paste your code or ask a question..." style="background: var(--bg-main); color: white; border: 1px solid var(--border-color);"></textarea>
        <div class="d-flex justify-content-between">
            <div>
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-image"></i> Upload</button>
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-code-slash"></i> Code</button>
            </div>
            <button class="btn btn-primary btn-sm px-4 rounded-pill">Send <i class="bi bi-send ms-1"></i></button>
        </div>
      </div>
    </div>
  </div>
</div>