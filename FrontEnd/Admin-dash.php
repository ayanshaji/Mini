<?php
session_start();

// Security check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.html');
    exit;
}

// You would typically fetch these stats from your database
// For now, I'll use placeholder data - replace with actual database queries
$totalArtworks = 1247; // SELECT COUNT(*) FROM artworks
$totalUsers = 832; // SELECT COUNT(*) FROM users
$totalReports = 12; // SELECT COUNT(*) FROM reports WHERE status = 'pending'
$totalSales = 45620; // SELECT SUM(price) FROM sales this month
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | PixelMuse</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
    }

    /* Sidebar Styles */
    .sidebar {
      width: 280px;
      background: rgba(34, 4, 4, 0.95);
      backdrop-filter: blur(20px);
      border-right: 1px solid rgba(255, 255, 255, 0.2);
      display: flex;
      flex-direction: column;
      position: fixed;
      height: 100vh;
      z-index: 100;
      transition: transform 0.3s ease;
    }

    .sidebar-header {
      padding: 2rem;
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: 800;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 0.5rem;
    }

    .logo-subtitle {
      color: #132c5eff;
      font-size: 0.9rem;
      font-weight: 500;
    }

    .sidebar-nav {
      padding: 1rem;
      flex: 1;
    }

    .nav-item {
      margin-bottom: 0.5rem;
    }

    .nav-link {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem 1.5rem;
      color: #374151;
      text-decoration: none;
      border-radius: 12px;
      transition: all 0.2s ease;
      font-weight: 500;
      position: relative;
      overflow: hidden;
    }

    .nav-link:hover {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      transform: translateX(5px);
    }

    .nav-link.active {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .nav-link i {
      font-size: 1.1rem;
      width: 20px;
    }

    .sidebar-footer {
      padding: 1.5rem;
      border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    /* Main Content */
    .main-content {
      margin-left: 280px;
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .admin-header {
      background: rgba(255, 255, 255, 0.43);
      backdrop-filter: blur(20px);
      padding: 1.5rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .header-left h1 {
      color: #1f2937;
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 0.25rem;
    }

    .header-subtitle {
      color: #6b7280;
      font-size: 0.9rem;
    }

    .header-right {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .admin-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
    }

    .logout-btn {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .logout-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.4);
    }

    .admin-content {
      padding: 2rem;
      flex: 1;
    }

    /* Dashboard Cards */
    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1rem;
    }

    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      color: white;
    }

    .stat-icon.artworks { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-icon.users { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .stat-icon.reports { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .stat-icon.sales { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

    .stat-value {
      font-size: 2.5rem;
      font-weight: 800;
      color: #1f2937;
      line-height: 1;
      margin-bottom: 0.5rem;
    }

    .stat-label {
      color: #6b7280;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.8rem;
      letter-spacing: 0.5px;
    }

    .stat-change {
      display: flex;
      align-items: center;
      gap: 0.25rem;
      font-size: 0.8rem;
      font-weight: 600;
      margin-top: 0.5rem;
    }

    .stat-change.positive { color: #10b981; }
    .stat-change.negative { color: #ef4444; }

    /* Charts Section */
    .charts-section {
      display: grid;
      grid-template-columns: 1.5fr 1fr;
      gap: 1.5rem;
      margin-bottom: 2rem;
      max-height: 350px;
    }

    .chart-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 16px;
      padding: 1.5rem;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.2);
      height: 320px;
    }

    .chart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .chart-title {
      font-size: 1.1rem;
      font-weight: 700;
      color: #1f2937;
    }

    .chart-subtitle {
      color: #6b7280;
      font-size: 0.85rem;
    }

    /* Recent Activity */
    .activity-section {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .activity-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem 0;
      border-bottom: 1px solid #f3f4f6;
    }

    .activity-item:last-child {
      border-bottom: none;
    }

    .activity-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
      color: white;
    }

    .activity-content {
      flex: 1;
    }

    .activity-title {
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 0.25rem;
    }

    .activity-time {
      color: #6b7280;
      font-size: 0.8rem;
    }

    /* Quick Actions */
    .quick-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .action-btn {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 15px;
      padding: 1.5rem;
      text-align: center;
      text-decoration: none;
      color: #374151;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .action-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .action-btn i {
      font-size: 2rem;
      margin-bottom: 0.5rem;
      display: block;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
      .charts-section {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }
      
      .main-content {
        margin-left: 0;
      }
      
      .dashboard-grid {
        grid-template-columns: 1fr;
      }
      
      .admin-header {
        padding: 1rem;
      }
      
      .admin-content {
        padding: 1rem;
      }
    }

    /* Loading Animation */
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

    .stat-card {
      animation: fadeInUp 0.6s ease-out backwards;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-header">
      <div class="logo">PixelMuse</div>
      <div class="logo-subtitle">Admin Panel</div>
    </div>
    
    <nav class="sidebar-nav">
      <div class="nav-item">
        <a href="admin-dashboard.php" class="nav-link active">
          <i class="fas fa-home"></i>
          <span>Dashboard</span>
        </a>
      </div>
      <div class="nav-item">
        <a href="Gallery.php" class="nav-link">
          <i class="fas fa-images"></i>
          <span>Gallery</span>
        </a>
      </div>
      <div class="nav-item">
        <a href="manage_reports.php" class="nav-link">
          <i class="fas fa-flag"></i>
          <span>Manage Reports</span>
        </a>
      </div>
      <div class="nav-item">
        <a href="#" class="nav-link">
          <i class="fas fa-users"></i>
          <span>Users</span>
        </a>
      </div>
      <div class="nav-item">
        <a href="#" class="nav-link">
          <i class="fas fa-chart-bar"></i>
          <span>Analytics</span>
        </a>
      </div>
      <div class="nav-item">
        <a href="#" class="nav-link">
          <i class="fas fa-cog"></i>
          <span>Settings</span>
        </a>
      </div>
    </nav>

    <div class="sidebar-footer">
      <a href="admin-logout.php" class="nav-link">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>

  <!-- Main content -->
  <div class="main-content">
    <div class="admin-header">
      <div class="header-left">
        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h1>
        <div class="header-subtitle">Here's what's happening with PixelMuse today.</div>
      </div>
      <div class="header-right">
        <div class="admin-avatar">
          <?php echo strtoupper(substr($_SESSION['admin_name'], 0, 1)); ?>
        </div>
        <form action="admin-logout.php" method="post">
          <button type="submit" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            Logout
          </button>
        </form>
      </div>
    </div>

    <div class="admin-content">
      <!-- Dashboard Statistics -->
      <div class="dashboard-grid">
        <div class="stat-card">
          <div class="stat-header">
            <div>
              <div class="stat-value"><?php echo number_format($totalArtworks); ?></div>
              <div class="stat-label">Total Artworks</div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <span>+12% from last month</span>
              </div>
            </div>
            <div class="stat-icon artworks">
              <i class="fas fa-palette"></i>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div>
              <div class="stat-value"><?php echo number_format($totalUsers); ?></div>
              <div class="stat-label">Active Users</div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <span>+8% from last month</span>
              </div>
            </div>
            <div class="stat-icon users">
              <i class="fas fa-users"></i>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div>
              <div class="stat-value"><?php echo $totalReports; ?></div>
              <div class="stat-label">Pending Reports</div>
              <div class="stat-change negative">
                <i class="fas fa-arrow-down"></i>
                <span>-5% from last week</span>
              </div>
            </div>
            <div class="stat-icon reports">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div>
              <div class="stat-value">$<?php echo number_format($totalSales); ?></div>
              <div class="stat-label">Monthly Sales</div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <span>+18% from last month</span>
              </div>
            </div>
            <div class="stat-icon sales">
              <i class="fas fa-dollar-sign"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="charts-section">
        <div class="chart-card">
          <div class="chart-header">
            <div>
              <div class="chart-title">Sales Overview</div>
              <div class="chart-subtitle">Monthly revenue trends</div>
            </div>
          </div>
          <canvas id="salesChart" width="400" height="200"></canvas>
        </div>

        <div class="chart-card">
          <div class="chart-header">
            <div>
              <div class="chart-title">Category Distribution</div>
              <div class="chart-subtitle">Artwork categories</div>
            </div>
          </div>
          <canvas id="categoryChart" width="200" height="200"></canvas>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="quick-actions">
        <a href="Gallery.php" class="action-btn">
          <i class="fas fa-images"></i>
          <div>View Gallery</div>
        </a>
        <a href="manage_reports.php" class="action-btn">
          <i class="fas fa-flag"></i>
          <div>Review Reports</div>
        </a>
        <a href="#" class="action-btn">
          <i class="fas fa-plus"></i>
          <div>Add Artwork</div>
        </a>
        <a href="#" class="action-btn">
          <i class="fas fa-chart-line"></i>
          <div>View Analytics</div>
        </a>
      </div>

      <!-- Recent Activity -->
      <div class="activity-section">
        <div class="chart-header">
          <div class="chart-title">Recent Activity</div>
        </div>
        
        <div class="activity-item">
          <div class="stat-icon artworks activity-icon">
            <i class="fas fa-palette"></i>
          </div>
          <div class="activity-content">
            <div class="activity-title">New artwork "Sunset Dreams" was uploaded</div>
            <div class="activity-time">2 hours ago</div>
          </div>
        </div>

        <div class="activity-item">
          <div class="stat-icon users activity-icon">
            <i class="fas fa-user-plus"></i>
          </div>
          <div class="activity-content">
            <div class="activity-title">New user registration: sarah_artist</div>
            <div class="activity-time">4 hours ago</div>
          </div>
        </div>

        <div class="activity-item">
          <div class="stat-icon reports activity-icon">
            <i class="fas fa-flag"></i>
          </div>
          <div class="activity-content">
            <div class="activity-title">Artwork report resolved: "Abstract Motion"</div>
            <div class="activity-time">6 hours ago</div>
          </div>
        </div>

        <div class="activity-item">
          <div class="stat-icon sales activity-icon">
            <i class="fas fa-shopping-cart"></i>
          </div>
          <div class="activity-content">
            <div class="activity-title">Sale completed: "Digital Landscape" - $299</div>
            <div class="activity-time">8 hours ago</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
          label: 'Sales ($)',
          data: [32000, 42000, 38000, 52000, 48000, 45620],
          borderColor: 'rgb(102, 126, 234)',
          backgroundColor: 'rgba(102, 126, 234, 0.1)',
          borderWidth: 3,
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '$' + value.toLocaleString();
              }
            }
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
      type: 'doughnut',
      data: {
        labels: ['Digital Art', 'Photography', 'Paintings', 'Illustrations', 'Mixed Media'],
        datasets: [{
          data: [35, 25, 20, 15, 5],
          backgroundColor: [
            'rgb(102, 126, 234)',
            'rgb(16, 185, 129)', 
            'rgb(245, 158, 11)',
            'rgb(239, 68, 68)',
            'rgb(139, 92, 246)'
          ],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 20,
              usePointStyle: true
            }
          }
        }
      }
    });

    // Add smooth scrolling
    document.documentElement.style.scrollBehavior = 'smooth';
  </script>
</body>
</html>