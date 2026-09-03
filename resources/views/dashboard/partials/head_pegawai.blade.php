<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title ?? 'Portal Pegawai - BBPSDMP' }}</title>

{{-- FONTS & ICONS --}}
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

{{-- CHART JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- GLOBAL STYLES (TEMA KATRING) --}}
<style>
    :root {
        --primary: #4361ee; --secondary: #3f37c9; --success: #4cc9f0; --warning: #f72585;
        --bg-light: #f3f4f6; --text-dark: #1f2937; --sidebar-width: 260px;
    }
    body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text-dark); overflow-x: hidden; }

    /* LAYOUT UTAMA */
    .wrapper { display: flex; width: 100%; min-height: 100vh; }

    /* SIDEBAR */
    .sidebar {
        width: var(--sidebar-width); background: white; padding: 20px;
        position: fixed; height: 100vh; overflow-y: auto; border-right: 1px solid #e5e7eb;
        z-index: 1000; transition: all 0.3s;
    }
    .brand { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; padding: 0 10px; }
    .brand-icon { width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .brand-text h5 { font-weight: 700; margin: 0; color: var(--text-dark); }
    .brand-text span { font-size: 0.75rem; color: #9ca3af; }

    .nav-item { list-style: none; margin-bottom: 8px; }
    .nav-link {
        display: flex; align-items: center; gap: 12px; padding: 12px 16px;
        color: #6b7280; text-decoration: none; border-radius: 12px; font-weight: 500; transition: all 0.2s;
    }
    .nav-link:hover, .nav-link.active { background-color: var(--primary); color: white; box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3); }
    .nav-link i { width: 20px; text-align: center; }

    /* UPGRADE PLAN BOX (Logout Area) */
    .upgrade-box {
        background: linear-gradient(135deg, #4361ee 0%, #7209b7 100%);
        border-radius: 16px; padding: 20px; color: white; text-align: center; margin-top: auto;
        position: absolute; bottom: 20px; width: calc(100% - 40px);
    }
    .upgrade-icon { width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; border: 2px solid rgba(255,255,255,0.3); }

    /* MAIN CONTENT */
    .main-content { margin-left: var(--sidebar-width); flex: 1; padding: 30px; }

    /* TOP BAR */
    .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .search-box { background: white; padding: 10px 20px; border-radius: 30px; display: flex; align-items: center; gap: 10px; width: 300px; border: 1px solid #e5e7eb; }
    .search-box button { background: none; border: none; }
    .search-box input { border: none; outline: none; width: 100%; color: #4b5563; }
    .user-profile { display: flex; align-items: center; gap: 15px; cursor: pointer; }
    .notif-btn { background: white; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; color: #6b7280; position: relative; border: none; }
    .notif-badge { position: absolute; top: 10px; right: 12px; width: 8px; height: 8px; background: var(--warning); border-radius: 50%; border: 1px solid white; }
    .profile-info { text-align: right; }
    .profile-info h6 { margin: 0; font-weight: 700; font-size: 0.9rem; }
    .profile-info small { color: #9ca3af; font-size: 0.75rem; }
    .profile-img { width: 45px; height: 45px; border-radius: 50%; background: var(--success); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }

    /* STAT CARDS */
    .stat-card { background: white; border-radius: 20px; padding: 24px; border: none; box-shadow: 0 2px 6px rgba(0,0,0,0.02); height: 100%; transition: transform 0.3s; position: relative; overflow: hidden; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
    .icon-box { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 15px; }
    .bg-icon-primary { background: rgba(67, 97, 238, 0.1); color: var(--primary); }
    .bg-icon-success { background: rgba(76, 201, 240, 0.1); color: var(--success); }
    .bg-icon-warning { background: rgba(247, 37, 133, 0.1); color: var(--warning); }

    /* TABLE STYLE */
    .table-card { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); }
    .table thead th { background: #f9fafb; font-size: 0.75rem; text-transform: uppercase; color: #6b7280; padding: 15px; border-bottom: none; font-weight: 700; }
    .table tbody td { padding: 15px; vertical-align: middle; color: #374151; font-weight: 500; font-size: 0.9rem; border-bottom: 1px solid #f3f4f6; }
    .status-badge { padding: 6px 12px; border-radius: 30px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }

    .btn-action { padding: 8px 16px; border-radius: 30px; font-size: 0.8rem; font-weight: 600; transition: 0.2s; }
    .btn-action:hover { transform: scale(1.05); }

    @media (max-width: 992px) {
        .sidebar { transform: translateX(-100%); }
        .main-content { margin-left: 0; }
    }
</style>
