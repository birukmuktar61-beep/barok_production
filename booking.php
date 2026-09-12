<?php
/**
 * BAROK PRODUCTION - Dedicated Online Booking Portal (5-Step Wizard)
 */

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';

try {
    $pdo = Database::getConnection();
    $stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY service_type ASC, price ASC");
    $services = $stmt->fetchAll();
    $siteSettings = getAllSettings($pdo);
} catch (Throwable $e) {
    $services = [];
    $siteSettings = getAllSettings();
}

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
    <title><?= __('booking_page_title') ?> — <?= __('app_name') ?></title>
    <meta name="description" content="<?= __('booking_page_subtitle') ?>">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Core Glassmorphism & Dark Mode CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* --- Service Type Selector Cards (Step 1) --- */
        .type-selector-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 10px;
        }
        .type-card {
            padding: 28px 20px;
            border-radius: var(--border-radius-md);
            border: 2px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.02);
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
        }
        .type-card:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 183, 3, 0.4);
            transform: translateY(-3px);
        }
        .type-card.selected {
            border-color: var(--accent-gold);
            background: rgba(255, 183, 3, 0.1);
            box-shadow: 0 0 30px rgba(255, 183, 3, 0.18);
        }
        .type-card-icon {
            font-size: 2.8rem;
            margin-bottom: 14px;
            display: block;
        }
        .type-card h4 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-primary);
        }
        .type-card p {
            font-size: 0.82rem;
            color: var(--text-muted);
            line-height: 1.5;
        }
        .type-card.selected h4 {
            color: var(--accent-gold);
        }

        /* --- Package Select Cards (Step 2) --- */
        .package-select-card {
            padding: 20px;
            border-radius: var(--border-radius-md);
            border: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.02);
            cursor: pointer;
            transition: var(--transition);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .package-select-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 183, 3, 0.3);
        }
        .package-select-card.selected {
            border-color: var(--accent-gold);
            background: rgba(255, 183, 3, 0.09);
            box-shadow: 0 0 20px rgba(255, 183, 3, 0.15);
        }
        .package-select-card.hidden-type {
            display: none !important;
        }
        .no-packages-msg {
            text-align: center;
            padding: 30px;
            color: var(--text-muted);
            display: none;
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
            <li><a href="booking.php" class="nav-link active"><?= __('nav_book_btn') ?></a></li>
        </ul>

        <div class="nav-actions">
            <!-- Language Switcher -->
            <?= renderLangSwitcher() ?>

            <a href="booking-status.php" class="btn btn-glass btn-sm">
                <span><?= __('nav_check_status') ?></span>
                <span>🔍</span>
            </a>
            <button class="mobile-menu-toggle" aria-label="Toggle navigation">☰</button>
        </div>
    </nav>

    <!-- Booking Header -->
    <div style="padding-top: 130px; padding-bottom: 20px; text-align: center;">
        <div class="container">
            <span class="section-tag"><?= __('booking_page_tag') ?></span>
            <h1 style="font-size: clamp(2rem, 4vw, 3rem); margin-bottom: 12px;"><?= __('booking_page_title') ?></h1>
            <p style="max-width: 650px; margin: 0 auto; color: var(--text-secondary);">
                <?= __('booking_page_subtitle') ?>
            </p>
        </div>
    </div>

    <!-- Booking Wizard Container -->
    <div class="container" style="padding-bottom: 80px;">
        <div class="glass-panel booking-wizard-wrapper">
            
            <!-- Step Indicators (5 steps) -->
            <div class="wizard-steps-indicator">
                <div class="step-node active" data-step="1">1</div>
                <div class="step-node" data-step="2">2</div>
                <div class="step-node" data-step="3">3</div>
                <div class="step-node" data-step="4">4</div>
                <div class="step-node" data-step="5">5</div>
            </div>

            <form id="bookingWizardForm">
                
                <!-- STEP 1: Service Type Selector -->
                <div class="wizard-step-panel" id="stepPanel1">
                    <h3 style="font-size: 1.4rem; margin-bottom: 8px;"><?= __('step_type_title') ?></h3>
                    <p style="margin-bottom: 28px;"><?= __('step_type_subtitle') ?></p>

                    <div class="type-selector-grid">
                        <!-- Photo -->
                        <div class="type-card" data-type="photo">
                            <span class="type-card-icon">📷</span>
                            <h4><?= __('type_photo') ?></h4>
                            <p><?= __('type_photo_desc') ?></p>
                        </div>
                        <!-- Video -->
                        <div class="type-card" data-type="video">
                            <span class="type-card-icon">🎬</span>
                            <h4><?= __('type_video') ?></h4>
                            <p><?= __('type_video_desc') ?></p>
                        </div>
                        <!-- Both -->
                        <div class="type-card" data-type="both">
                            <span class="type-card-icon">📷🎬</span>
                            <h4><?= __('type_both') ?></h4>
                            <p><?= __('type_both_desc') ?></p>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Select Package (filtered by type) -->
                <div class="wizard-step-panel" id="stepPanel2" style="display: none;">
                    <h3 style="font-size: 1.4rem; margin-bottom: 8px;"><?= __('step_1_title') ?></h3>
                    <p style="margin-bottom: 24px;"><?= __('step_1_subtitle') ?></p>

                    <div class="package-list" id="packageList">
                        <?php foreach ($services as $svc): ?>
                            <div class="package-select-card hidden-type" 
                                 data-id="<?= $svc['id'] ?>"
                                 data-title="<?= htmlspecialchars($svc['title']) ?>"
                                 data-price="<?= $svc['price'] ?>"
                                 data-type="<?= htmlspecialchars($svc['service_type'] ?? 'photo') ?>">
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    <input type="radio" name="service_radio" value="<?= $svc['id'] ?>" style="accent-color: var(--accent-gold); width: 20px; height: 20px; flex-shrink: 0;">
                                    <div>
                                        <h4 style="font-size: 1.05rem; margin-bottom: 4px;"><?= htmlspecialchars($svc['title']) ?></h4>
                                        <span style="font-size: 0.82rem; color: var(--text-muted);">
                                            <?= htmlspecialchars($svc['category']) ?> • <?= $svc['duration_hours'] ?> <?= __('service_hours') ?>
                                        </span>
                                        <p style="font-size: 0.82rem; color: var(--text-secondary); margin-top: 4px;">
                                            <?= htmlspecialchars($svc['short_desc']) ?>
                                        </p>
                                    </div>
                                </div>
                                <div style="text-align: right; flex-shrink: 0;">
                                    <span style="font-family: var(--font-display); font-size: 1.35rem; font-weight: 800; color: var(--accent-gold);">
                                        <?= formatPrice((float)$svc['price']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="no-packages-msg" id="noPackagesMsg">
                        No packages found for the selected type. Please go back and choose a different type.
                    </div>
                </div>

                <!-- STEP 3: Date, Time & Location -->
                <div class="wizard-step-panel" id="stepPanel3" style="display: none;">
                    <h3 style="font-size: 1.4rem; margin-bottom: 8px;"><?= __('step_2_title') ?></h3>
                    <p style="margin-bottom: 24px;"><?= __('step_2_subtitle') ?></p>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
                        <!-- Dual Calendar Date Picker -->
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label"><?= __('field_date') ?></label>

                            <!-- Calendar Type Tabs -->
                            <div style="display:flex; gap:0; border:1px solid rgba(255,255,255,0.15); border-radius:8px; overflow:hidden; margin-bottom:14px; width:fit-content;">
                                <button type="button" id="tabEth" onclick="switchCalTab('eth')"
                                    style="padding:8px 20px; background:var(--accent-gold); color:#000; font-weight:600; font-size:0.85rem; border:none; cursor:pointer;">
                                    🗓 <?= __('cal_tab_ethiopian') ?>
                                </button>
                                <button type="button" id="tabGreg" onclick="switchCalTab('greg')"
                                    style="padding:8px 20px; background:transparent; color:var(--text-muted); font-size:0.85rem; border:none; cursor:pointer;">
                                    📅 <?= __('cal_tab_gregorian') ?>
                                </button>
                            </div>

                            <!-- Ethiopian Calendar Panel -->
                            <div id="ethPanel" style="display:block;">
                                <div style="display:grid; grid-template-columns:2fr 2fr 1fr; gap:10px;">
                                    <div>
                                        <label class="form-label" style="font-size:0.78rem;"><?= __('cal_select_month') ?></label>
                                        <select id="ethMonth" class="form-select" onchange="ethToGreg()">
                                            <?php
                                            $ethMonths = [
                                                1=>'1 - መስከረም (Meskerem)',2=>'2 - ጥቅምት (Tikimt)',3=>'3 - ህዳር (Hidar)',
                                                4=>'4 - ታህሳስ (Tahsas)',5=>'5 - ጥር (Tir)',6=>'6 - የካቲት (Yekatit)',
                                                7=>'7 - መጋቢት (Megabit)',8=>'8 - ሚያዚያ (Miyazia)',9=>'9 - ግንቦት (Ginbot)',
                                                10=>'10 - ሰኔ (Sene)',11=>'11 - ሐምሌ (Hamle)',12=>'12 - ነሐሴ (Nehase)',
                                                13=>'13 - ጳጉሜ (Pagume)',
                                            ];
                                            foreach($ethMonths as $n=>$label): ?>
                                            <option value="<?= $n ?>"><?= $label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label" style="font-size:0.78rem;"><?= __('cal_select_day') ?></label>
                                        <select id="ethDay" class="form-select" onchange="ethToGreg()">
                                            <?php for($d=1;$d<=30;$d++): ?>
                                            <option value="<?= $d ?>"><?= $d ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label" style="font-size:0.78rem;"><?= __('cal_select_year') ?></label>
                                        <select id="ethYear" class="form-select" onchange="ethToGreg()">
                                            <?php for($y=2015;$y<=2020;$y++): ?>
                                            <option value="<?= $y ?>" <?= $y===2017?'selected':'' ?>><?= $y ?> ዓ.ም.</option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Gregorian Calendar Panel -->
                            <div id="gregPanel" style="display:none;">
                                <input type="date" id="bookingDateGreg" class="form-input" onchange="gregToEth()" style="max-width:260px;">
                            </div>

                            <!-- Hidden actual input for form submission -->
                            <input type="hidden" id="bookingDate" name="booking_date" required>

                            <!-- Dual Date Preview -->
                            <div id="calPreview" style="margin-top:12px; padding:12px 16px; background:rgba(212,175,55,0.08); border:1px solid rgba(212,175,55,0.25); border-radius:8px; font-size:0.85rem; display:none;">
                                <strong style="color:var(--accent-gold);"><?= __('cal_selected_preview') ?>:</strong>
                                <span id="calPreviewText" style="color:var(--text-primary); margin-left:8px;"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="bookingTimeSlot"><?= __('field_time_slot') ?></label>
                            <select id="bookingTimeSlot" name="booking_time" class="form-select">
                                <option value="09:00 AM - 01:00 PM"><?= __('slot_morning') ?></option>
                                <option value="01:30 PM - 05:30 PM" selected><?= __('slot_afternoon') ?></option>
                                <option value="06:00 PM - 10:00 PM"><?= __('slot_evening') ?></option>
                                <option value="Full Day (09:00 AM - 07:00 PM)"><?= __('slot_full_day') ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="shootLocation"><?= __('field_location') ?></label>
                        <input type="text" id="shootLocation" name="shoot_location" class="form-input" placeholder="<?= __('field_location_placeholder') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="projectNotes"><?= __('field_notes') ?></label>
                        <textarea id="projectNotes" name="project_notes" class="form-textarea" placeholder="<?= __('field_notes_placeholder') ?>"></textarea>
                    </div>
                </div>

                <!-- STEP 4: Add-on Extras -->
                <div class="wizard-step-panel" id="stepPanel4" style="display: none;">
                    <h3 style="font-size: 1.4rem; margin-bottom: 8px;"><?= __('step_3_title') ?></h3>
                    <p style="margin-bottom: 24px;"><?= __('step_3_subtitle') ?></p>

                    <div class="addons-grid">
                        <label class="addon-card">
                            <input type="checkbox" class="addon-checkbox" data-title="<?= htmlspecialchars(__('addon_drone_title')) ?>" data-price="4500">
                            <div class="addon-info">
                                <h4><?= __('addon_drone_title') ?></h4>
                                <span class="addon-price">+ETB 4,500.00</span>
                                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;"><?= __('addon_drone_desc') ?></p>
                            </div>
                        </label>

                        <label class="addon-card">
                            <input type="checkbox" class="addon-checkbox" data-title="<?= htmlspecialchars(__('addon_rush_title')) ?>" data-price="3500">
                            <div class="addon-info">
                                <h4><?= __('addon_rush_title') ?></h4>
                                <span class="addon-price">+ETB 3,500.00</span>
                                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;"><?= __('addon_rush_desc') ?></p>
                            </div>
                        </label>

                        <label class="addon-card">
                            <input type="checkbox" class="addon-checkbox" data-title="<?= htmlspecialchars(__('addon_audio_title')) ?>" data-price="2800">
                            <div class="addon-info">
                                <h4><?= __('addon_audio_title') ?></h4>
                                <span class="addon-price">+ETB 2,800.00</span>
                                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;"><?= __('addon_audio_desc') ?></p>
                            </div>
                        </label>

                        <label class="addon-card">
                            <input type="checkbox" class="addon-checkbox" data-title="<?= htmlspecialchars(__('addon_makeup_title')) ?>" data-price="2200">
                            <div class="addon-info">
                                <h4><?= __('addon_makeup_title') ?></h4>
                                <span class="addon-price">+ETB 2,200.00</span>
                                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;"><?= __('addon_makeup_desc') ?></p>
                            </div>
                        </label>

                        <label class="addon-card">
                            <input type="checkbox" class="addon-checkbox" data-title="<?= htmlspecialchars(__('addon_ssd_title')) ?>" data-price="1800">
                            <div class="addon-info">
                                <h4><?= __('addon_ssd_title') ?></h4>
                                <span class="addon-price">+ETB 1,800.00</span>
                                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;"><?= __('addon_ssd_desc') ?></p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- STEP 5: Client Info & Final Review -->
                <div class="wizard-step-panel" id="stepPanel5" style="display: none;">
                    <h3 style="font-size: 1.4rem; margin-bottom: 8px;"><?= __('step_4_title') ?></h3>
                    <p style="margin-bottom: 24px;"><?= __('step_4_subtitle') ?></p>

                    <!-- Review Card -->
                    <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border); border-radius: var(--border-radius-md); padding: 22px; margin-bottom: 26px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid var(--glass-border); padding-bottom: 12px;">
                            <span style="font-weight: 700; color: var(--accent-gold);"><?= __('summary_selected_pkg') ?></span>
                            <span id="summaryService" style="font-weight: 700;">—</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid var(--glass-border); padding-bottom: 12px;">
                            <span style="font-weight: 700; color: var(--accent-gold);"><?= __('summary_schedule') ?></span>
                            <span id="summaryDate">—</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid var(--glass-border); padding-bottom: 12px;">
                            <span style="font-weight: 700; color: var(--accent-gold);"><?= __('summary_location') ?></span>
                            <span id="summaryLocation">—</span>
                        </div>
                        <div style="margin-bottom: 12px;">
                            <span style="font-weight: 700; color: var(--accent-gold); display: block; margin-bottom: 6px;"><?= __('summary_addons') ?></span>
                            <ul id="summaryAddonsList" style="list-style: none; padding-left: 0; font-size: 0.9rem;"></ul>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 14px; border-top: 1px solid rgba(255, 183, 3, 0.3);">
                            <span style="font-size: 1.1rem; font-weight: 700;"><?= __('summary_total_est') ?></span>
                            <span id="summaryTotalReview" style="font-family: var(--font-display); font-size: 1.8rem; font-weight: 800; color: var(--accent-gold);">ETB 0.00</span>
                        </div>
                    </div>

                    <!-- Contact Details Form -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px;">
                        <div class="form-group">
                            <label class="form-label" for="clientName"><?= __('field_client_name') ?></label>
                            <input type="text" id="clientName" name="client_name" class="form-input" placeholder="<?= __('field_client_name_placeholder') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="clientEmail"><?= __('field_client_email') ?></label>
                            <input type="email" id="clientEmail" name="client_email" class="form-input" placeholder="<?= __('field_client_email_placeholder') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="clientPhone"><?= __('field_client_phone') ?></label>
                            <input type="tel" id="clientPhone" name="client_phone" class="form-input" placeholder="<?= __('field_client_phone_placeholder') ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Live Price Bar & Step Navigation Buttons -->
                <div class="booking-summary-bar">
                    <div>
                        <span class="summary-total-label"><?= __('summary_est_total') ?></span>
                        <div class="summary-total-value" id="liveTotalPrice">ETB 0.00</div>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button type="button" class="btn btn-glass" id="prevStepBtn" style="display: none;">
                            <span><?= __('btn_back') ?></span>
                        </button>
                        <button type="button" class="btn btn-primary" id="nextStepBtn">
                            <span><?= __('btn_next') ?></span>
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBookingBtn" style="display: none;">
                            <span><?= __('btn_confirm_book') ?></span>
                        </button>
                    </div>
                </div>

            </form>
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

    <!-- Pass Translation Tokens to JavaScript -->
    <script>
        window.BAROK_I18N = {
            lang: <?= json_encode(getCurrentLang()) ?>,
            noAddons: <?= json_encode(__('summary_no_addons')) ?>,
            confirmedTitle: <?= json_encode(__('booking_confirmed_title')) ?>,
            confirmedDesc: <?= json_encode(__('booking_confirmed_desc')) ?>,
            refLabel: <?= json_encode(__('booking_ref_label')) ?>,
            refHint: <?= json_encode(__('booking_ref_hint')) ?>,
            btnTrack: <?= json_encode(__('btn_track_status')) ?>,
            btnHome: <?= json_encode(__('btn_return_home')) ?>,
            selectTypeToast: <?= json_encode(getCurrentLang() === 'am' ? 'እባክዎ የአገልግሎት ዓይነት ይምረጡ (ፎቶ፣ ቪዲዮ ወይም ሁለቱም)።' : 'Please select a service type: Photo, Video, or Both.') ?>,
            selectPkgToast: <?= json_encode(getCurrentLang() === 'am' ? 'እባክዎ የፕሮዳክሽን ጥቅል ይምረጡ።' : 'Please choose a production package.') ?>,
            selectDateToast: <?= json_encode(getCurrentLang() === 'am' ? 'እባክዎ የቀረጻውን ቀን ይምረጡ።' : 'Please select your preferred shoot date.') ?>,
            selectLocToast: <?= json_encode(getCurrentLang() === 'am' ? 'እባክዎ የቀረጻ ቦታ ወይም የስቱዲዮ አድራሻ ያስገቡ።' : 'Please provide a shoot location or studio address.') ?>,
            fillContactToast: <?= json_encode(getCurrentLang() === 'am' ? 'እባክዎ ስምዎትን፣ ኢሜይል እና ስልክ ቁጥርዎን ያስገቡ።' : 'Please fill in your name, email, and phone number.') ?>
        };
    </script>
    <script src="assets/js/main.js?v=2.2"></script>
    <script src="assets/js/ethiopian_calendar.js?v=2.3"></script>
    <script>
    // ── Ethiopian ↔ Gregorian Calendar Picker Sync ──────────────────────────
    const ETH_MONTH_NAMES_EN = ['','Meskerem','Tikimt','Hidar','Tahsas','Tir','Yekatit','Megabit','Miyazia','Ginbot','Sene','Hamle','Nehase','Pagume'];
    const ETH_MONTH_NAMES_AM = ['','መስከረም','ጥቅምት','ህዳር','ታህሳስ','ጥር','የካቲት','መጋቢት','ሚያዚያ','ግንቦት','ሰኔ','ሐምሌ','ነሐሴ','ጳጉሜ'];

    function switchCalTab(tab) {
        const ethP = document.getElementById('ethPanel');
        const gregP = document.getElementById('gregPanel');
        const tabE = document.getElementById('tabEth');
        const tabG = document.getElementById('tabGreg');
        if (tab === 'eth') {
            ethP.style.display = 'block'; gregP.style.display = 'none';
            tabE.style.background = 'var(--accent-gold)'; tabE.style.color = '#000'; tabE.style.fontWeight = '600';
            tabG.style.background = 'transparent'; tabG.style.color = 'var(--text-muted)'; tabG.style.fontWeight = 'normal';
        } else {
            ethP.style.display = 'none'; gregP.style.display = 'block';
            tabG.style.background = 'var(--accent-gold)'; tabG.style.color = '#000'; tabG.style.fontWeight = '600';
            tabE.style.background = 'transparent'; tabE.style.color = 'var(--text-muted)'; tabE.style.fontWeight = 'normal';
        }
    }

    function updateDayOptions(year, month) {
        const sel = document.getElementById('ethDay');
        const curVal = parseInt(sel.value) || 1;
        const maxDay = (month === 13) ? ((year % 4 === 3) ? 6 : 5) : 30;
        sel.innerHTML = '';
        for (let d = 1; d <= maxDay; d++) {
            const opt = document.createElement('option');
            opt.value = d; opt.textContent = d;
            if (d === Math.min(curVal, maxDay)) opt.selected = true;
            sel.appendChild(opt);
        }
    }

    function ethToGreg() {
        const year  = parseInt(document.getElementById('ethYear').value);
        const month = parseInt(document.getElementById('ethMonth').value);
        const day   = parseInt(document.getElementById('ethDay').value);
        updateDayOptions(year, month);
        if (!window.EthiopicDate) return;
        const greg = EthiopicDate.toGregorian(year, month, day);
        if (!greg) return;
        const yy = greg.year, mm = String(greg.month).padStart(2,'0'), dd = String(greg.day).padStart(2,'0');
        const isoDate = `${yy}-${mm}-${dd}`;
        document.getElementById('bookingDateGreg').value = isoDate;
        document.getElementById('bookingDate').value = isoDate;
        showCalPreview(greg, {year, month, day});
    }

    function gregToEth() {
        const val = document.getElementById('bookingDateGreg').value;
        if (!val || !window.EthiopicDate) return;
        const [gy, gm, gd] = val.split('-').map(Number);
        const eth = EthiopicDate.fromGregorian(gy, gm, gd);
        if (!eth) return;
        document.getElementById('ethYear').value = eth.year;
        document.getElementById('ethMonth').value = eth.month;
        updateDayOptions(eth.year, eth.month);
        document.getElementById('ethDay').value = eth.day;
        document.getElementById('bookingDate').value = val;
        showCalPreview({year:gy, month:gm, day:gd}, eth);
    }

    function showCalPreview(greg, eth) {
        const preview = document.getElementById('calPreview');
        const text = document.getElementById('calPreviewText');
        const gDate = new Date(greg.year, greg.month - 1, greg.day);
        const gregStr = gDate.toLocaleDateString('en-US', {year:'numeric', month:'long', day:'numeric'});
        const ethStr = `${ETH_MONTH_NAMES_EN[eth.month]} ${eth.day}, ${eth.year} E.C. (${ETH_MONTH_NAMES_AM[eth.month]} ${eth.day}, ${eth.year} ዓ.ም.)`;
        text.textContent = `${gregStr}  ↔  ${ethStr}`;
        preview.style.display = 'block';
    }

    // Initialize: set Ethiopian picker to today's equivalent
    document.addEventListener('DOMContentLoaded', function() {
        if (window.EthiopicDate) {
            const today = new Date();
            const eth = EthiopicDate.fromGregorian(today.getFullYear(), today.getMonth() + 1, today.getDate());
            if (eth) {
                const yr = document.getElementById('ethYear');
                if (yr) {
                    // Select matching year if in range, otherwise select last option
                    let found = false;
                    for (let o of yr.options) { if (parseInt(o.value) === eth.year) { o.selected = true; found = true; break; } }
                    if (!found) yr.options[yr.options.length - 1].selected = true;
                }
                const mo = document.getElementById('ethMonth');
                if (mo) mo.value = eth.month;
                updateDayOptions(eth.year, eth.month);
                const dy = document.getElementById('ethDay');
                if (dy) dy.value = eth.day;
                ethToGreg();
            }
        }
    });
    </script>
    <script src="assets/js/booking.js?v=2.2"></script>
</body>
</html>
