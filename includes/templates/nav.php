<!-- Navbar -->
<nav class="navbar navbar-expand-md navbar-dark bg-dark ">
    <div class="container">
        <a href="index.php" class=" navbar navbar-brand mr-auto active">
            <i class="fas fa-home mr-1"></i>
            <?php echo lang('HOME_ADMIN') ?></a>
        <button class="navbar-toggler ml-auto" data-toggle="collapse" data-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="categories.php" class="nav-link">
                        <?php echo lang('CATEGORIES') ?> </a>
                </li>
                <li class="nav-item">
                    <a href="items.php" class="nav-link">
                        <?php echo lang('ITEMS') ?>
                    </a>
                </li>
                <li class="nav-item ">
                    <a href="members.php" class="nav-link">
                        <?php echo lang('MEMBERS') ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="comments.php" class="nav-link">
                        <?php echo lang('COMMENTS') ?>
                    </a>
                </li>
            </ul>
            <!-- drop down menu -->
            <ul class="navbar-nav ml-auto navbar-sm">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user fa-fw"></i><?php echo  $_SESSION['Username'] ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="members.php?do=edit&userId=<?php echo $_SESSION['ID'] ?>">Edit Profile</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>