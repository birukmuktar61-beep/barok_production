<?php
/**
 * BAROK PRODUCTION - Client Booking Status Tracker
 */

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';

$refCode = strtoupper(trim(sanitize($_GET['ref'] ?? '')));
$booking = null;
$searched = !empty($refCode);
$errorMsg = null;

if ($searched) {
    try {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT 
                b.id,
                b.reference_code,
                b.client_name,
                b.client_email,
                b.booking_date,
                b.booking_time,
                b.shoot_location,
                b.project_notes,
                b.add_ons,
                b.total_price,
                b.status,
                b.admin_notes,
                b.created_at,
                s.title AS service_title,
                s.category AS service_category,
                s.duration_hours
            FROM bookings b
            JOIN services s ON b.service_id = s.id
            WHERE b.reference_code = :ref
            LIMIT 1
        ");
        $stmt->execute([':ref' => $refCode]);
        $booking = $stmt->fetch();

        if (!$booking) {
            $errorMsg = __('status_not_found') . $refCode;
        } else {
            $booking['add_ons'] = json_decode($booking['add_ons'] ?? '[]', true) ?: [];
        }
    } catch (Throwable $e) {
        $errorMsg = "Database lookup error. Please try again.";
    }
}

// Dynamic Site Settings for Footer
$siteSettings   = getAllSettings();
$appPhone       = $siteSettings['app_phone'] ?? APP_PHONE;
$appPhoneRaw    = $siteSettings['app_phone_raw'] ?? APP_PHONE_RAW;
$socialTelegram = $siteSettings['social_telegram'] ?? SOCIAL_TELEGRAM;
$socialInstagram= $siteSettings['social_instagram'] ?? SOCIAL_INSTAGRAM;
$socialTiktok   = $siteSettings['social_tiktok'] ?? SOCIAL_TIKTOK;
$socialYoutube  = $siteSettings['social_youtube'] ?? SOCIAL_YOUTUBE;
$socialFacebook = $siteSettings['social_facebook'] ?? SOCIAL_FACEBOOK;
?>
<!DOCTYPE html>
<html lang="<?= getCurrentLang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('status_page_title') ?> — <?= __('app_name') ?></title>
    <meta name="description" content="<?= __('status_page_subtitle') ?>">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Core Glassmorphism & Dark Mode CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: var(--border-radius-full);
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .status-pending {
            background: rgba(245, 158, 11, 0.15);
            color: var(--status-pending);
            border: 1px solid rgba(245, 158, 11, 0.4);
        }
        .status-confirmed {
            background: rgba(16, 185, 129, 0.15);
            color: var(--status-confirmed);
            border: 1px solid rgba(16, 185, 129, 0.4);
        }
        .status-completed {
            background: rgba(59, 130, 246, 0.15);
            color: var(--status-completed);
            border: 1px solid rgba(59, 130, 246, 0.4);
        }
        .status-cancelled {
            background: rgba(239, 68, 68, 0.15);
            color: var(--status-cancelled);
            border: 1px solid rgba(239, 68, 68, 0.4);
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <nav class="navbar">
        <a href="index.php" class="brand-logo">
            <span class="logo-icon">B</span>
            <span>BAROK<span style="color: var(--accent-gold);">.</span></span>
        </a>

        <ul class="nav-links">
            <li><a href="index.php" class="nav-link"><?= __('nav_home') ?></a></li>
            <li><a href="index.php#services" class="nav-link"><?= __('nav_services') ?></a></li>
            <li><a href="index.php#portfolio" class="nav-link"><?= __('nav_portfolio') ?></a></li>
            <li><a href="index.php#reviews" class="nav-link"><?= __('nav_testimonials') ?></a></li>
            <li><a href="booking.php" class="nav-link"><?= __('nav_book_btn') ?></a></li>
        </ul>

        <div class="nav-actions">
            <!-- Language Switcher -->
            <?= renderLangSwitcher() ?>

            <a href="booking.php" class="btn btn-primary btn-sm">
                <span><?= __('nav_book_btn') ?></span>
                <span>→</span>
            </a>
            <button class="mobile-menu-toggle" aria-label="Toggle navigation">☰</button>
        </div>
    </nav>

    <!-- Page Content -->
    <div style="padding-top: 130px; padding-bottom: 80px;">
        <div class="container" style="max-width: 760px;">
            
            <div style="text-align: center; margin-bottom: 36px;">
                <span class="section-tag"><?= __('status_page_tag') ?></span>
                <h1 style="font-size: clamp(2rem, 3.5vw, 2.6rem); margin-bottom: 12px;"><?= __('status_page_title') ?></h1>
                <p style="color: var(--text-secondary);"><?= __('status_page_subtitle') ?></p>
            </div>

            <!-- Lookup Form -->
            <div class="glass-panel" style="padding: 24px; margin-bottom: 30px;">
                <form method="GET" action="booking-status.php" style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <?php if (isset($_GET['lang'])): ?>
                        <input type="hidden" name="lang" value="<?= htmlspecialchars($_GET['lang']) ?>">
                    <?php endif; ?>
                    <input type="text" name="ref" value="<?= htmlspecialchars($refCode) ?>" class="form-input" placeholder="<?= __('status_input_placeholder') ?>" style="flex: 1; min-width: 240px;" required>
                    <button type="submit" class="btn btn-primary">
                        <span><?= __('status_search_btn') ?></span>
                        <span>🔍</span>
                    </button>
                </form>
            </div>

            <?php if ($errorMsg): ?>
                <div class="glass-panel" style="padding: 24px; text-align: center; border-color: var(--status-cancelled); background: rgba(239, 68, 68, 0.05); margin-bottom: 30px;">
                    <div style="font-size: 1.5rem; margin-bottom: 8px;">⚠️</div>
                    <p style="color: var(--status-cancelled); font-weight: 600;"><?= htmlspecialchars($errorMsg) ?></p>
                </div>
            <?php endif; ?>

            <?php if ($booking): 
                $statusKey = 'status_badge_' . $booking['status'];
                $translatedStatus = __($statusKey, ucfirst(str_replace('_', ' ', $booking['status'])));
            ?>
                <div class="glass-panel" style="padding: 36px 30px; border-color: rgba(255, 183, 3, 0.4);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
                        <div>
                            <span style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); letter-spacing: 1px; display: block;"><?= __('booking_ref_label') ?></span>
                            <span style="font-family: var(--font-display); font-size: 1.8rem; font-weight: 800; color: var(--accent-gold);"><?= htmlspecialchars($booking['reference_code']) ?></span>
                        </div>
                        <div>
                            <span class="status-badge status-<?= htmlspecialchars($booking['status']) ?>">
                                ● <?= htmlspecialchars($translatedStatus) ?>
                            </span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 26px; border-top: 1px solid var(--glass-border); padding-top: 20px;">
                        <div>
                            <span style="font-size: 0.8rem; color: var(--text-muted); display: block;"><?= __('status_client_label') ?></span>
                            <strong style="font-size: 1rem;"><?= htmlspecialchars($booking['client_name']) ?></strong>
                        </div>
                        <div>
                            <span style="font-size: 0.8rem; color: var(--text-muted); display: block;"><?= __('status_package_label') ?></span>
                            <strong style="font-size: 1rem; color: var(--accent-gold);"><?= htmlspecialchars($booking['service_title']) ?></strong>
                        </div>
                        <div>
                            <span style="font-size: 0.8rem; color: var(--text-muted); display: block;"><?= __('status_date_label') ?></span>
                            <strong style="font-size: 1rem;"><?= formatDateDual($booking['booking_date']) ?></strong>
                        </div>
                        <div>
                            <span style="font-size: 0.8rem; color: var(--text-muted); display: block;"><?= __('status_time_label') ?></span>
                            <strong style="font-size: 1rem;"><?= htmlspecialchars($booking['booking_time']) ?></strong>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <span style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-bottom: 4px;"><?= __('status_loc_label') ?></span>
                        <p style="background: rgba(255, 255, 255, 0.02); padding: 12px 16px; border-radius: var(--border-radius-sm); border: 1px solid var(--glass-border); font-size: 0.95rem;">
                            📍 <?= htmlspecialchars($booking['shoot_location']) ?>
                        </p>
                    </div>

                    <?php if (!empty($booking['add_ons'])): ?>
                        <div style="margin-bottom: 20px;">
                            <span style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-bottom: 6px;"><?= __('status_addons_label') ?></span>
                            <ul style="list-style: none; padding-left: 0;">
                                <?php foreach ($booking['add_ons'] as $addon): ?>
                                    <li style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 4px;">
                                        ✦ <?= htmlspecialchars($addon['title'] ?? '') ?> (<?= formatPrice((float)($addon['price'] ?? 0)) ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($booking['admin_notes'])): ?>
                        <div style="margin-bottom: 20px; background: rgba(255, 183, 3, 0.06); padding: 16px; border-radius: var(--border-radius-sm); border-left: 4px solid var(--accent-gold);">
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--accent-gold); display: block; margin-bottom: 4px;"><?= __('status_note_label') ?></span>
                            <p style="font-size: 0.9rem; color: var(--text-primary);"><?= nl2br(htmlspecialchars($booking['admin_notes'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--glass-border); padding-top: 20px; margin-top: 10px;">
                        <div>
                            <span style="font-size: 0.8rem; color: var(--text-muted);"><?= __('status_fee_label') ?></span>
                            <div style="font-family: var(--font-display); font-size: 1.6rem; font-weight: 800; color: var(--accent-gold);">
                                <?= formatPrice((float)$booking['total_price']) ?>
                            </div>
                        </div>
                        <div>
                            <a href="index.php#contact" class="btn btn-glass btn-sm">
                                <span><?= __('status_contact_team') ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Footer -->
    <footer class="footer" style="margin-top: 60px;">
        <div class="container">
            <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px; padding-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.06);">
                <div>
                    <a href="index.php" class="brand-logo" style="display: inline-flex; margin-bottom: 8px;">
                        <span class="logo-icon">B</span>
                        <span>BAROK<span style="color: var(--accent-gold);">.</span></span>
                    </a>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">
                        📍 <?= APP_LOCATION ?> &bull; 
                        <a href="tel:<?= htmlspecialchars($appPhoneRaw) ?>" class="phone-link" style="display: inline-flex;">📞 <?= htmlspecialchars($appPhone) ?></a> &bull; 
                        <a href="mailto:<?= APP_EMAIL ?>" style="color: var(--text-muted); text-decoration:none;">✉️ <?= APP_EMAIL ?></a>
                    </p>
                </div>
                <div class="social-links" style="margin-top: 0;">
                    <a href="<?= htmlspecialchars($socialTelegram) ?>" target="_blank" rel="noopener" class="social-btn" title="Telegram">
                        <svg viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.194 1.006.131.833.941z"/></svg>
                        Telegram
                    </a>
                    <a href="<?= htmlspecialchars($socialInstagram) ?>" target="_blank" rel="noopener" class="social-btn" title="Instagram">
                        <svg viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        Instagram
                    </a>
                    <a href="<?= htmlspecialchars($socialTiktok) ?>" target="_blank" rel="noopener" class="social-btn" title="TikTok">
                        <svg viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        TikTok
                    </a>
                    <a href="<?= htmlspecialchars($socialYoutube) ?>" target="_blank" rel="noopener" class="social-btn" title="YouTube">
                        <svg viewBox="0 0 24 24"><path d="M23.495 6.205a3.007 3.007 0 00-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 00.527 6.205a31.247 31.247 0 00-.522 5.805 31.247 31.247 0 00.522 5.783 3.007 3.007 0 002.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 002.088-2.088 31.247 31.247 0 00.5-5.783 31.247 31.247 0 00-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>
                        YouTube
                    </a>
                    <a href="<?= htmlspecialchars($socialFacebook) ?>" target="_blank" rel="noopener" class="social-btn" title="Facebook">
                        <svg viewBox="0 0 24 24"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073c0 6.03 4.388 11.02 10.125 11.927v-8.434H7.078v-3.493h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.268h3.328l-.532 3.493h-2.796v8.434C19.612 23.093 24 18.102 24 12.073z"/></svg>
                        Facebook
                    </a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. <?= __('footer_copyright') ?></p>
            </div>
        </div>
    </footer>

    <!-- Client Scripts -->
    <script src="assets/js/main.js?v=2.2"></script>
</body>
</html>
