<?php
require '../../vendor/autoload.php'; 
use PhpOffice\PhpWord\IOFactory;

// Include database connection
include '../../db_connect.php';

// Get the event ID from the query string
$event_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($event_id) {
    // Fetch the event details
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();

    // Check if the event exists
    if ($event) {
        // Split the image URLs into an array
        $imageUrls = explode(',', $event['image_url']);

        // Increment the views count for the event
        $updateViewsQuery = $conn->prepare("UPDATE events SET views = views + 1 WHERE id = ?");
        $updateViewsQuery->bind_param("i", $event_id);
        $updateViewsQuery->execute();

        // Fetch other events for the sidebar or suggestions
        $stmt_other = $conn->prepare("SELECT id, event_name FROM events WHERE id != ? LIMIT 5");
        $stmt_other->bind_param("i", $event_id);
        $stmt_other->execute();
        $other_events = $stmt_other->get_result();

        // Handle comment and rating form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_name = isset($_POST['user_name']) ? htmlspecialchars($_POST['user_name']) : null;
            $rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;
            $comment = isset($_POST['comment']) ? htmlspecialchars($_POST['comment']) : null;

            if ($user_name && $rating && $comment) {
                // Insert the comment and rating into the database
                $stmt_comment = $conn->prepare("INSERT INTO event_comments (event_id, user_name, rating, comment) VALUES (?, ?, ?, ?)");
                $stmt_comment->bind_param("isis", $event_id, $user_name, $rating, $comment);

                if ($stmt_comment->execute()) {
                    // Increment the comment count for the event
                    $stmt_update_comments = $conn->prepare("UPDATE events SET comments = comments + 1 WHERE id = ?");
                    $stmt_update_comments->bind_param("i", $event_id);
                    $stmt_update_comments->execute();

                    // Success message for the user
                    $success_message = "<div class='alert alert-success'>Thank you for your comment!</div>";
                } else {
                    // Error saving the comment
                    $error_message = "<div class='alert alert-danger'>Error saving your comment. Please try again later.</div>";
                }
            }
        }
    } else {
        // Event not found
        $error_message = "<div class='alert alert-danger'>Event not found. Please check the ID.</div>";
    }
} else {
    // Invalid or missing event ID
    $error_message = "<div class='alert alert-danger'>Invalid Event ID. Please provide a valid ID.</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../../images/logo.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/viewer.css">
</head>
<body>
   <div class="container">
       <div class="banner border mt-3">
            <h1 class="text-center m-3">Event Viewer</h1>
       </div>
       <!-- Breadcrumb Navigation -->
       <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../../index.html">Home</a> / </li>
                <li class="breadcrumb-item"><a href="../events.php">Events</a> / </li>
                <li class="breadcrumb-item" aria-current="page">Event Viewer</li>
            </ol>
       </nav>
       <!-- Event Section -->
       <div class="row">
           <div class="col-md-9">
               <div class="article border">
                   <?php if (isset($error_message)): ?>
                       <div class="alert alert-danger"><?= $error_message ?></div>
                   <?php else: ?>
                       <!-- Event Header -->
                       <div class="article-header p-3">
                           <h1 class="article-title mb-3"><?= htmlspecialchars($event['event_name']) ?></h1>
                           <div class="article-meta d-flex align-items-center mb-3">
                               <img src="../../images/my.jpeg" alt="Author" class="rounded-circle me-2" width="40" height="40">
                               <div class="d-flex flex-column">
                                   <span class="article-author fw-bold">By: Dr. Abdullah Al-Swidi</span>
                                   <span class="text-muted"><?= htmlspecialchars($event['created_at']) ?></span>
                               </div>
                           </div>
                           <hr class="my-2">
                           <!-- Event Stats -->
                           <div class="article-stats d-flex justify-content-between align-items-center pe-2">
                               <div class="d-flex gap-3">
                                   <span class="views text-muted">
                                       <i class="fa fa-eye me-1"></i> <?= htmlspecialchars($event['views'] ?? "0") ?> Views
                                   </span>
                                   <span class="comments text-muted">
                                       <i class="fa fa-comment me-1"></i> <?= htmlspecialchars($event['comments'] ?? "0") ?> Comments
                                   </span>
                               </div>
                               <div class="social-share">
                                   <span class="text-muted me-2">Share:</span>
                                   <i class="fa fa-facebook me-2 cursor-pointer" onclick="shareOnFacebook()"></i>
                                   <i class="fa fa-twitter me-2 cursor-pointer" onclick="shareOnTwitter()"></i>
                                   <i class="fa fa-linkedin cursor-pointer" onclick="shareOnLinkedIn()"></i>
                               </div>
                           </div>
                           <hr class="my-2">
                       </div>
                       <!-- Event Content -->
                       <div class="article-content">
                           <p><strong>Event Date:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
                           <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                           <hr>
                           <p><?= htmlspecialchars($event['description']) ?></p>
                           <?php if (!empty($event['image_url'])): ?>
                               <div class="row">
                                   <?php foreach ($imageUrls as $imageUrl): ?>
                                       <div class="col-md-6 mb-3">
                                           <img src="../../<?= htmlspecialchars(trim($imageUrl)) ?>" alt="Event Image" class="img-fluid rounded">
                                       </div>
                                   <?php endforeach; ?>
                               </div>
                           <?php endif; ?>
                           <?php if (!empty($event['link'])): ?>
                            For more information, visit: <a href="<?= htmlspecialchars($event['link']) ?>" target="_blank"><?= htmlspecialchars($event['link']) ?></a>
                           <?php endif; ?>
                       </div>
                   <?php endif; ?>
               </div>
               <hr class="my-4">

               <!-- Form for Comment and Interactive Star Rating -->
               <h3 class="section-header border rounded-top">Leave a Comment and Rate the Event:</h3>
               <form method="POST" class="form-control shadow-sm p-4 border-secondary">
                   <div class="form-group mt-3">
                       <label for="user_name" class="form-label">Your Name:</label>
                       <input type="text" id="user_name" name="user_name" class="form-control border-secondary" placeholder="Enter your name" required>
                   </div>
                   <div class="form-group mt-4">
                       <label for="rating" class="form-label">Rating:</label>
                       <div class="star-rating mt-2">
                           <input type="radio" id="star5" name="rating" value="5">
                           <label for="star5" class="fa fa-star"></label>
                           <input type="radio" id="star4" name="rating" value="4">
                           <label for="star4" class="fa fa-star"></label>
                           <input type="radio" id="star3" name="rating" value="3">
                           <label for="star3" class="fa fa-star"></label>
                           <input type="radio" id="star2" name="rating" value="2">
                           <label for="star2" class="fa fa-star"></label>
                           <input type="radio" id="star1" name="rating" value="1">
                           <label for="star1" class="fa fa-star"></label>
                       </div>
                   </div>
                   <div class="form-group mt-4">
                       <label for="comment" class="form-label">Your Comment:</label>
                       <textarea id="comment" name="comment" class="form-control border-secondary" rows="4" placeholder="Write your comment here"></textarea>
                   </div>
                   <button type="submit" class="btn btn-primary rounded-top mt-4 w-100">Submit Comment</button>
               </form>
           </div>
           <!-- Sidebar Section -->
           <div class="col-md-3">
               <div class="article border">
                   <div class="section-header text-white p-2 rounded-top">
                       <h5>Other Events</h5>
                   </div>
                   <?php if (isset($error_message)): ?>
                       <div class="alert alert-danger"><?= $error_message ?></div>
                   <?php else: ?>
                       <div class="article-body">
                           <ul class="list-group">
                               <?php while ($other = $other_events->fetch_assoc()): ?>
                                   <li class="list-group-item">
                                       <a href="?id=<?= $other['id'] ?>" class="text-decoration-none text-primary">
                                           <i class="fa fa-calendar-alt"></i> <?= htmlspecialchars($other['event_name']) ?>
                                       </a>
                                   </li>
                               <?php endwhile; ?>
                           </ul>
                       </div>
                   <?php endif; ?>
               </div>
               <!-- Comments Section -->
               <div class="article border rounded shadow-sm mt-2">
                   <div class="section-header text-white p-2 rounded-top">
                       <h5>Comments and Ratings</h5>
                   </div>
                   <div class="article-body p-2">
                       <?php
                       // Fetch comments and ratings for the current event
                       $stmt_comments = $conn->prepare("SELECT * FROM event_comments WHERE event_id = ? ORDER BY created_at DESC");
                       $stmt_comments->bind_param("i", $event_id);
                       $stmt_comments->execute();
                       $comments = $stmt_comments->get_result();

                       if ($comments->num_rows > 0):
                           while ($comment = $comments->fetch_assoc()):
                       ?>
                           <div class="comment mb-4 border-bottom">
                               <div class="d-flex justify-content-between align-items-center">
                                   <div class="star-rating readonly">
                                       <?php
                                       for ($i = 1; $i <= 5; $i++) {
                                           echo $i <= $comment['rating'] 
                                               ? '<i class="fa fa-star text-warning"></i>' 
                                               : '<i class="bi bi-star text-muted"></i>';
                                       }
                                       ?>
                                   </div>
                                   <span ><?= htmlspecialchars($comment['user_name']) ?> <i class="fa-solid fa-user-tie me-2"></i></span>
                                   
                               </div>
                               <p class="mt-2 text-muted message p-2"><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
                           </div>
                       <?php endwhile; ?>
                       <?php else: ?>
                           <p class="text-muted">No comments yet. Be the first to share your thoughts!</p>
                       <?php endif; ?>
                   </div>
               </div>
               <script>
                   document.querySelectorAll('.star-rating:not(.readonly) label').forEach(star => {
                       star.addEventListener('click', function () {
                           this.style.transform = 'scale(1.2)';
                           setTimeout(() => {
                               this.style.transform = 'scale(1)';
                           }, 200);
                       });
                   });
               </script>
           </div>
       </div>
       <!-- Footer -->
       <footer class="footer mt-3">
           <div class="container">
               <div class="row">
                   <div class="col-md-4 text-center">
                       <h5><a href="../index.html" class="navbar-brand fw-bold">Dr. Abdullah Al-Swidi</a></h5>
                       <p>Driving Excellence and Innovation in Sustainability</p>
                   </div>
                   <div class="col-md-4 text-center">
                       <p>Visitors:<br><span id="visitor-count" class="animated-counter">0</span></p>
                   </div>
                   <div class="col-md-4 text-center">
                       <div id="google_translate_element"></div>
                   </div>
               </div>
               <hr>
               <div class="text-center">
                   <p>&copy; 2024 Dr. Abdullah Al-Swidi. All rights reserved.</p>
               </div>
           </div>
       </footer>
   </div>

   <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
   <script src="../../js/counter.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
   <script>
       function googleTranslateElementInit() {
           new google.translate.TranslateElement(
               { pageLanguage: 'en', includedLanguages: 'en,ar', layout: google.translate.TranslateElement.InlineLayout.SIMPLE },
               'google_translate_element'
           );
       }
   </script>
   <script type="text/javascript">
       // JavaScript for generating shareable links
       const eventUrl = window.location.href;
       function shareOnFacebook() {
           const facebookUrl = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(eventUrl);
           window.open(facebookUrl, '_blank', 'width=600,height=400');
       }
       function shareOnTwitter() {
           const twitterUrl = "https://twitter.com/intent/tweet?url=" + encodeURIComponent(eventUrl) + "&text=Check out this event!";
           window.open(twitterUrl, '_blank', 'width=600,height=400');
       }
       function shareOnLinkedIn() {
           const linkedInUrl = "https://www.linkedin.com/sharing/share-offsite/?url=" + encodeURIComponent(eventUrl);
           window.open(linkedInUrl, '_blank', 'width=600,height=400');
       }
   </script>
</body>
</html>