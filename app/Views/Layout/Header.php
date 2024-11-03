<header>
    <nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm align-middle">
        <div class="container px-3">
            <a class="navbar-brand fw-bold" href="<?= site_url() ?>">
                 <svg id="Glyph" width="32" height="32" enable-background="new 0 0 32 32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                    <path fill="white" d="m5.959 7.905c.137-.607-.069-1.905-.959-1.905-.739 0-1 .823-1 1 0 .553-.447 1-1 1s-1-.447-1-1c0-1.227 1.068-3 3-3 2.273 0 3.356 2.376 2.909 4.348-.717 3.148-2.909 4.128-2.909 6.652 0 2.571 1.32 4.075 2.381 4.872-.46.483-.87 1.011-1.23 1.574-1.426-1.074-3.151-3.065-3.151-6.446 0-3.349 2.342-4.386 2.959-7.095z" />
                    <path fill="white" d="m20.692 5c-.483-1.948-1.389-2.438-2.692-3-1.303.562-2.209 1.052-2.692 3-2.758.531-5.726 2.105-3.474 6.104.092.163.287.244.466.19.87-.264 3.476-.992 5.701-.992s4.831.729 5.701.992c.179.054.374-.028.466-.19 2.25-3.999-.717-5.573-3.476-6.104zm-6.442 3.5c-.414 0-.75-.336-.75-.75s.336-.75.75-.75.75.336.75.75-.336.75-.75.75zm3.75 0-1.125-1.5 1.125-1.5 1.125 1.5zm3.75 0c-.414 0-.75-.336-.75-.75s.336-.75.75-.75.75.336.75.75-.336.75-.75.75z" />
                    <path fill="white" d="m17.665 17.671-1-.5c-.371-.186-.521-.636-.336-1.006.187-.371.636-.521 1.006-.336l.665.332.665-.332c.371-.186.82-.035 1.006.336.186.37.035.82-.336 1.006l-1 .5c-.209.104-.457.106-.67 0z" />
                    <path fill="white" d="m19 12v1c0 .552-.448 1-1 1s-1-.448-1-1v-1z" />
                    <path fill="white" d="m25.853 19.532c-.003-.002-.004-.006-.007-.008-.008-.006-.017-.006-.025-.012-.496-.312-1.025-.576-1.583-.787.484-.726.762-1.497.762-2.225 0-.842-.358-1.644-1.042-2.337v-1.379c-.426-.009-.918-.122-1.5-.273v1.976c0 .216.093.421.255.563.359.315.787.823.787 1.45 0 1.555-2.464 3.938-5.5 3.938s-5.5-2.383-5.5-3.938c0-.627.428-1.135.787-1.45.162-.143.255-.348.255-.563v-1.976c-.582.151-1.074.265-1.5.273v1.379c-.684.693-1.042 1.495-1.042 2.337 0 .726.277 1.496.76 2.222-3.414 1.292-5.76 4.58-5.76 8.344v2.184c0 .414.336.75.75.75h22.5c.414 0 .75-.336.75-.75v-2.184c0-3.135-1.631-5.937-4.147-7.534zm-2.677.409c.29.086.571.19.844.308-1.511 1.729-3.71 2.751-6.02 2.751s-4.508-1.022-6.02-2.75c.273-.118.554-.223.844-.308 1.279 1.155 3.118 1.996 5.176 1.996s3.898-.842 5.176-1.997zm5.324 8.559h-3v-1.75c0-.414-.336-.75-.75-.75s-.75.336-.75.75v1.75h-12v-1.75c0-.414-.336-.75-.75-.75s-.75.336-.75.75v1.75h-3v-1.434c0-2.304 1.058-4.392 2.746-5.766 1.68 2.102 4.122 3.773 6.754 4.121v.579c0 .553.447 1 1 1s1-.447 1-1v-.578c2.633-.347 5.074-2.018 6.754-4.121 1.688 1.374 2.746 3.461 2.746 5.766z" />
                </svg>
                <?= config('App')->appName ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <?php if (session()->has('userid')) : ?>
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link text-white fw-bold" href="<?= site_url('keys') ?>">Keys</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white fw-bold" href="<?= site_url('keys/generate') ?>">Generate</a>
                        </li>
                    </ul>
                    <div class="float-right">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-white fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle pe-2"></i><?= getName($user) ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start" aria-labelledby="navbarDropdown">
                                    <li>
                                        <a class="dropdown-item" href="<?= site_url('settings') ?>">
                                            <i class="bi bi-gear"></i> Settings
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <?php if ($user->level == 1) : ?>
                                        <li class="dropdown-item text-muted">Admin</li>
                                        <li>
                                            <a class="dropdown-item" href="<?= site_url('admin/manage-users') ?>">
                                                <i class="bi bi-person-check"></i> Manage Users
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?= site_url('admin/create-referral') ?>">
                                                <i class="bi bi-person-plus"></i> Create Referral
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?= site_url('admin/apkmanager') ?>">
                                                <i class="bi bi-robot"></i> APK Manager
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                    <?php endif; ?>
                                    <li>
                                        <a class="dropdown-item text-danger" href="<?= site_url('logout') ?>">
                                            <i class="bi bi-box-arrow-in-left"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
            </div>
        <?php endif; ?>

        </div>
    </nav>
</header>