<?php
session_start();

if(empty($_SESSION['id_company'])) {
  header("Location: ../index.php");
  exit();
}

require_once("../db.php");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>CareerHub</title>
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
                  <li><a href="my-job-post.php"><i class="fa fa-file-o"></i> My Job Post</a></li>
                  <li class="active"><a href="job-applications.php"><i class="fa fa-file-o"></i> Job Applications</a></li>
                  <li><a href="mailbox.php"><i class="fa fa-envelope"></i> Mailbox</a></li>
                  <li><a href="settings.php"><i class="fa fa-gear"></i> Settings</a></li>
                  <li><a href="resume-database.php"><i class="fa fa-user"></i> Resume Database</a></li>
                  <li><a href="../logout.php"><i class="fa fa-arrow-circle-o-right"></i> Logout</a></li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-9 bg-white padding-2">
            <h2><i>Recent Applications</i></h2>
            <?php
            $sql = "SELECT ajp.*, jp.jobtitle, u.firstname, u.lastname, ajp.full_name, ajp.email, ajp.phone, 
                    ajp.experience, ajp.skills, ajp.resume_path, ajp.cover_letter_path, ajp.application_date 
                    FROM apply_job_post ajp 
                    INNER JOIN job_post jp ON jp.id_jobpost=ajp.id_jobpost 
                    INNER JOIN users u ON u.id_user=ajp.id_user 
                    WHERE ajp.id_company=? 
                    ORDER BY ajp.application_date DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION['id_company']);
            $stmt->execute();
            $result = $stmt->get_result();

            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
            ?>
            <div class="attachment-block clearfix padding-2">
                <h4 class="attachment-heading">
                    <a href="user-application.php?id=<?php echo $row['id_user']; ?>&id_jobpost=<?php echo $row['id_jobpost']; ?>">
                        <?php echo htmlspecialchars($row['jobtitle'].' - '.($row['full_name'])); ?>
                    </a>
                </h4>
                <div class="attachment-text padding-2">
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                    <p><strong>Experience:</strong> <?php echo htmlspecialchars($row['experience']); ?> years</p>
                    <p><strong>Skills:</strong> <?php echo htmlspecialchars($row['skills']); ?></p>
                    <div class="pull-left"><i class="fa fa-calendar"></i> <?php echo date("M d, Y", strtotime($row['application_date'])); ?></div>
                    <?php 
                    if($row['status'] == 0) {
                        echo '<div class="pull-right"><strong class="text-orange">Pending</strong></div>';
                    } else if ($row['status'] == 1) {
                        echo '<div class="pull-right"><strong class="text-red">Rejected</strong></div>';
                    } else if ($row['status'] == 2) {
                        echo '<div class="pull-right"><strong class="text-green">Under Review</strong></div>';
                    }
                    ?>
                     <br>
                     <br>
                    <p><strong>Resume:</strong>
                        <?php if (!empty($row['resume_path'])): ?>
                            <!-- Correctly reference the path for the resume -->
                            <a href="uploads/resumes/<?php echo basename($row['resume_path']); ?>" download>Download</a>
                        <?php else: ?>
                            Not provided
                        <?php endif; ?>
                    </p>
                    <p><strong>Cover Letter:</strong>
                        <?php if (!empty($row['cover_letter_path'])): ?>
                            <!-- Correctly reference the path for the cover letter -->
                            <a href="uploads/cover_letters/<?php echo basename($row['cover_letter_path']); ?>" download>Download</a>
                        <?php else: ?>
                            Not provided
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php
                }
            }
            ?>
          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer" style="margin-left: 0px;">
    <div class="text-center">
      <strong>Copyright &copy; 2016-2017 <a href="jonsnow.netai.net">CareerHub</a>.</strong> All rights reserved.
    </div>
  </footer>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="../js/adminlte.min.js"></script>
</body>
</html>
