<?php
session_start();
if (empty($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

require_once("db.php");

if (isset($_GET['id'])) {
    $sql = "SELECT * FROM job_post WHERE id_jobpost=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $job = $result->fetch_assoc();
        $id_company = $job['id_company'];
    } else {
        header("Location: jobs.php");
        exit();
    }

    $sql1 = "SELECT * FROM apply_job_post WHERE id_user=? AND id_jobpost=?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("ii", $_SESSION['id_user'], $job['id_jobpost']);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    if ($result1->num_rows > 0) {
        echo "<script>alert('You have already applied for this job!'); window.location.href='jobs.php';</script>";
        exit();
    }
}

if (isset($_POST['submit'])) {
    $resume_path = '';
    $cover_letter_path = '';

    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $resume_path = 'company/uploads/resumes/' . time() . '_' . $_FILES['resume']['name'];
        move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path);
    }

    if (isset($_FILES['cover_letter']) && $_FILES['cover_letter']['error'] === UPLOAD_ERR_OK) {
        $cover_letter_path = 'company/uploads/cover_letters/' . time() . '_' . $_FILES['cover_letter']['name'];
        move_uploaded_file($_FILES['cover_letter']['tmp_name'], $cover_letter_path);
    }

    $sql = "INSERT INTO apply_job_post (id_jobpost, id_company, id_user, full_name, email, phone, experience, skills, resume_path, cover_letter_path, status, createdat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiisssssss", $_POST['job_id'], $id_company, $_SESSION['id_user'], $_POST['full_name'], $_POST['email'], $_POST['phone'], $_POST['experience'], $_POST['skills'], $resume_path, $cover_letter_path);

    if ($stmt->execute()) {
        echo "<script>alert('Application submitted successfully!'); window.location.href='jobs.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply for Job</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h2>Job Application Form</h2>

                <?php if (isset($job)): ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Job Details</h3>
                    </div>
                    <div class="panel-body">
                        <p><strong>Position:</strong> <?php echo htmlspecialchars($job['jobtitle']); ?></p>
                        <p><strong>Description:</strong> <?php echo nl2br(strip_tags($job['description'])); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="job_id" value="<?php echo $_GET['id']; ?>">
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" class="form-control" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label>Years of Experience</label>
                        <input type="number" class="form-control" name="experience" required>
                    </div>

                    <div class="form-group">
                        <label>Skills</label>
                        <textarea class="form-control" name="skills" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Resume (PDF only)</label>
                        <input type="file" class="form-control" name="resume" accept=".pdf" required>
                    </div>

                    <div class="form-group">
                        <label>Cover Letter (PDF only)</label>
                        <input type="file" class="form-control" name="cover_letter" accept=".pdf" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 30px;">
                        <button type="submit" name="submit" class="btn btn-primary">Submit Application</button>
                        <a href="jobs.php" class="btn btn-default" style="margin-left: 10px;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
