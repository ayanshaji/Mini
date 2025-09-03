<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "pixel";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all reports with artwork details
$query = "SELECT r.*, a.title, a.image_path 
          FROM reports r
          JOIN artworks a ON r.artwork_id = a.id
          ORDER BY r.reported_at DESC";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Artwork Reports - PixelMuse</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1687c8ff 0%, #d81992ff 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            text-align: center;
        }

        .header h1 {
            color: #1f2937;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .report-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .artwork-image-container {
            position: relative;
            height: 250px;
            overflow: hidden;
            background: linear-gradient(45deg, #f3f4f6, #e5e7eb);
        }

        .artwork-thumbnail {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .report-card:hover .artwork-thumbnail {
            transform: scale(1.05);
        }

        .report-status {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(239, 68, 68, 0.9);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .report-content {
            padding: 1.5rem;
        }

        .artwork-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .report-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .report-meta i {
            color: #ef4444;
        }

        .report-reason {
            margin-bottom: 1.5rem;
        }

        .reason-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #374151;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .reason-text {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 1rem;
            border-radius: 8px;
            color: #7f1d1d;
            line-height: 1.5;
            font-size: 0.9rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn {
            flex: 1;
            min-width: 120px;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-dismiss {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-dismiss:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
        }

        .btn-remove {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .btn-remove:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.4);
        }

        .no-reports {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .no-reports .icon {
            font-size: 4rem;
            color: #10b981;
            margin-bottom: 1.5rem;
        }

        .no-reports h2 {
            color: #1f2937;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .no-reports p {
            color: #6b7280;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .dismissed {
            opacity: 0.7;
            transform: scale(0.98);
        }

        .dismissed .report-status {
            background: rgba(16, 185, 129, 0.9);
        }

        /* Loading animation */
        .loading {
            position: relative;
            overflow: hidden;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .reports-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                min-width: auto;
            }
        }

        /* Smooth animations */
        @media (prefers-reduced-motion: no-preference) {
            .report-card {
                animation: fadeInUp 0.6s ease-out backwards;
            }
            
            .report-card:nth-child(1) { animation-delay: 0.1s; }
            .report-card:nth-child(2) { animation-delay: 0.2s; }
            .report-card:nth-child(3) { animation-delay: 0.3s; }
            .report-card:nth-child(4) { animation-delay: 0.4s; }
            .report-card:nth-child(5) { animation-delay: 0.5s; }
            .report-card:nth-child(6) { animation-delay: 0.6s; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-flag"></i> Reported Artworks</h1>
            <p>Review and manage reported artwork content</p>
        </div>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="reports-grid">
                <?php while ($report = $result->fetch_assoc()): ?>
                    <div class="report-card" data-report-id="<?= $report['id'] ?>">
                        <div class="artwork-image-container">
                            <img src="<?= htmlspecialchars($report['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($report['title']) ?>" 
                                 class="artwork-thumbnail">
                            <div class="report-status">
                                <i class="fas fa-exclamation-triangle"></i> Reported
                            </div>
                        </div>
                        
                        <div class="report-content">
                            <h3 class="artwork-title"><?= htmlspecialchars($report['title']) ?></h3>
                            
                            <div class="report-meta">
                                <i class="fas fa-clock"></i>
                                <span>Reported on <?= date('M j, Y g:i a', strtotime($report['reported_at'])) ?></span>
                            </div>
                            
                            <div class="report-reason">
                                <div class="reason-label">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Report Reason</span>
                                </div>
                                <div class="reason-text">
                                    <?= nl2br(htmlspecialchars($report['reason'])) ?>
                                </div>
                            </div>
                            
                            <div class="action-buttons">
                                <button class="btn btn-dismiss" data-report-id="<?= $report['id'] ?>">
                                    <i class="fas fa-check"></i>
                                    <span>Dismiss Report</span>
                                </button>
                                <button class="btn btn-remove" data-artwork-id="<?= $report['artwork_id'] ?>">
                                    <i class="fas fa-trash"></i>
                                    <span>Remove Artwork</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-reports">
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2>No Reported Artworks</h2>
                <p>Great news! There are currently no artwork reports to review.<br>All content appears to be in good standing.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Add functionality to action buttons
        document.querySelectorAll('.btn-dismiss').forEach(btn => {
            btn.addEventListener('click', function() {
                const reportId = this.getAttribute('data-report-id');
                const reportCard = this.closest('.report-card');
                const statusBadge = reportCard.querySelector('.report-status');
                
                // Add loading state
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Dismissing...</span>';
                this.disabled = true;
                
                fetch('../BackEnd/dismiss_report.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `report_id=${reportId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        reportCard.classList.add('dismissed');
                        statusBadge.innerHTML = '<i class="fas fa-check"></i> Dismissed';
                        this.innerHTML = '<i class="fas fa-check"></i> <span>Dismissed</span>';
                        this.style.background = 'linear-gradient(135deg, #6b7280 0%, #4b5563 100%)';
                        
                        // Auto-remove after 3 seconds
                        setTimeout(() => {
                            reportCard.style.transform = 'translateX(100%)';
                            reportCard.style.opacity = '0';
                            setTimeout(() => reportCard.remove(), 300);
                        }, 3000);
                    } else {
                        // Restore button on error
                        this.innerHTML = '<i class="fas fa-check"></i> <span>Dismiss Report</span>';
                        this.disabled = false;
                        alert('Error dismissing report. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.innerHTML = '<i class="fas fa-check"></i> <span>Dismiss Report</span>';
                    this.disabled = false;
                    alert('Error dismissing report. Please try again.');
                });
            });
        });

        document.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (confirm('Are you sure you want to permanently remove this artwork? This action cannot be undone.')) {
                    const artworkId = this.getAttribute('data-artwork-id');
                    const reportCard = this.closest('.report-card');
                    
                    // Add loading state
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Removing...</span>';
                    this.disabled = true;
                    
                    try {
                        const response = await fetch('../BackEnd/remove_artwork.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `artwork_id=${artworkId}`
                        });
                        
                        const data = await response.json();
                        
                        if (!response.ok || !data.success) {
                            throw new Error(data.error || 'Failed to remove artwork');
                        }
                        
                        // Animate removal
                        reportCard.style.transform = 'scale(0.8)';
                        reportCard.style.opacity = '0';
                        
                        setTimeout(() => {
                            reportCard.remove();
                            
                            // Check if no more reports
                            if (document.querySelectorAll('.report-card').length === 0) {
                                location.reload();
                            }
                        }, 300);
                        
                        console.log('Artwork removed successfully');
                    } catch (error) {
                        console.error('Error:', error);
                        this.innerHTML = '<i class="fas fa-trash"></i> <span>Remove Artwork</span>';
                        this.disabled = false;
                        alert(error.message || 'Error removing artwork. Please try again.');
                    }
                }
            });
        });

        // Add smooth scrolling
        document.documentElement.style.scrollBehavior = 'smooth';
    </script>
</body>
</html>
<?php
$conn->close();
?>