<?php
session_start();

if (empty($_SESSION['id_company'])) {
    header("Location: ../index.php");
    exit();
}

require_once("../db.php");

$sql = "SELECT apply_job_post.*, users.* FROM apply_job_post 
        INNER JOIN users ON apply_job_post.id_user = users.id_user 
        WHERE apply_job_post.id_company = ? 
        AND apply_job_post.id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $_SESSION['id_company'], $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Job Portal</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="../css/AdminLTE.min.css">
    <link rel="stylesheet" href="../css/_all-skins.min.css">
    <link rel="stylesheet" href="../css/custom.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition skin-green sidebar-mini">
    <div class="wrapper">

        <header class="main-header">
            <a href="index.php" class="logo logo-bg">
                <span class="logo-mini"><b>C</b>H</span>
                <span class="logo-lg"><b>CareerHub</b></span>
            </a>
            <nav class="navbar navbar-static-top">
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                    </ul>
                </div>
            </nav>
        </header>

        <div class="content-wrapper" style="margin-left: 0px;">
            <section id="candidates" class="content-header">
                <div class="container">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="box box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Welcome <b><?php echo $_SESSION['name']; ?></b></h3>
                                </div>
                                <div class="box-body no-padding">
                                    <ul class="nav nav-pills nav-stacked">
                                        <li><a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                                        <li><a href="edit-company.php"><i class="fa fa-tv"></i> My Company</a></li>
                                        <li><a href="create-job-post.php"><i class="fa fa-file-o"></i> Create Job Post</a></li>
                                        <li class="active"><a href="my-job-post.php"><i class="fa fa-file-o"></i> My Job Post</a></li>
                                        <li><a href="job-applications.php"><i class="fa fa-file-o"></i> Job Application</a></li>
                                        <li><a href="mailbox.php"><i class="fa fa-envelope"></i> Mailbox</a></li>
                                        <li><a href="settings.php"><i class="fa fa-gear"></i> Settings</a></li>
                                        <li><a href="resume-database.php"><i class="fa fa-user"></i> Resume Database</a></li>
                                        <li><a href="../logout.php"><i class="fa fa-arrow-circle-o-right"></i> Logout</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9 bg-white padding-2">
                            <div class="row margin-top-20">
                                <div class="col-md-12">
                                    <?php if (isset($_SESSION['applicationSuccess'])): ?>
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h4><i class="icon fa fa-check"></i> Success!</h4>
                                        Application status has been updated successfully.
                                        <?php unset($_SESSION['applicationSuccess']); ?>
                                    </div>
                                    <?php endif; ?>

                                    <?php
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <div class="pull-right">
                                        <a href="job-applications.php" class="btn btn-default btn-lg btn-flat margin-top-20">
                                            <i class="fa fa-arrow-circle-left"></i> Back
                                        </a>
                                    </div>
                                    <div class="clearfix"></div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4>Personal Information</h4>
                                            <p><strong>Full Name:</strong> <?php echo $row['full_name']; ?></p>
                                            <p><strong>Email:</strong> <?php echo $row['email']; ?></p>
                                            <p><strong>Phone:</strong> <?php echo $row['phone']; ?></p>
                                            <p><strong>Experience:</strong> <?php echo $row['experience']; ?> years</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h4>Application Documents</h4>
                                            <?php
                                            if ($row['resume_path'] != "") {
                                                echo '<p><a href="uploads/resumes/' . basename($row['resume_path']) . '" class="btn btn-info" target="_blank">
                                                        <i class="fa fa-download"></i> Download Resume
                                                      </a></p>';
                                            }

                                            if ($row['cover_letter_path'] != "") {
                                                echo '<p><a href="uploads/cover_letters/' . basename($row['cover_letter_path']) . '" class="btn btn-info" target="_blank">
                                                        <i class="fa fa-download"></i> Download Cover Letter
                                                      </a></p>';
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="row margin-top-20" style="margin-bottom: 30px;">
                                        <div class="col-md-3 pull-left">
                                            <a href="under-review.php?id=<?php echo $row['id_user']; ?>&id_jobpost=<?php echo $_GET['id_jobpost']; ?>"
                                               class="btn btn-success">Mark Under Review</a>
                                        </div>
                                        <div class="col-md-3 pull-right">
                                            <a href="reject.php?id=<?php echo $row['id_user']; ?>&id_jobpost=<?php echo $_GET['id_jobpost']; ?>"
                                               class="btn btn-danger">Reject Application</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <footer class="main-footer" style="margin-left: 0px;">
            <div class="text-center">
                <strong>Copyright &copy; 2024 <a href="jonsnow.netai.net">CareerHub</a>.</strong> All rights reserved.
            </div>
        </footer>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="../js/adminlte.min.js"></script>
</body>

</html>
