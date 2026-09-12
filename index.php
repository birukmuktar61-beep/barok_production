<?php
/**
 * BAROK PRODUCTION - Official Portfolio & Booking Portal
 */

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';

// Fetch dynamic portfolio items & services from database
try {
    $pdo = Database::getConnection();

    // Services
    $serviceStmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY service_type ASC, price ASC");
    $services = $serviceStmt->fetchAll();

    // Portfolio
    $portfolioStmt = $pdo->query("SELECT * FROM portfolio ORDER BY sort_order ASC, id DESC");
    $portfolioItems = $portfolioStmt->fetchAll();

    // Approved Testimonials
    $testimonialStmt = $pdo->query("SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY id DESC LIMIT 9");
    $testimonials = $testimonialStmt->fetchAll();

    // Dynamic Site Settings & Metrics
    $siteSettings = getAllSettings($pdo);
} catch (Throwable $e) {
    $services = [];
    $portfolioItems = [];
    $testimonials = [];
    $siteSettings = getAllSettings();
}

$heroVideoUrl   = $siteSettings['hero_video_url'] ?? 'https://www.youtube.com/embed/dQw4w9WgXcQ';
$heroBgImage    = $siteSettings['hero_bg_image'] ?? 'https://images.unsplash.com/photo-1518135714426-c18f5ffb6f4d?auto=format&fit=crop&w=1600&q=80';
$statShoots     = $siteSettings['stat_shoots_num'] ?? __('stat_shoots_num');
$statRating     = $siteSettings['stat_rating_num'] ?? __('stat_rating_num');
$statAwards     = $siteSettings['stat_awards_num'] ?? __('stat_awards_num');
$statHdr        = $siteSettings['stat_hdr_num'] ?? __('stat_hdr_num');
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
    <title><?= __('app_name') ?> — <?= __('app_tagline') ?></title>
    <meta name="description" content="<?= __('hero_tagline') ?> — <?= __('hero_tagline_sub') ?>">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Core Glassmorphism & Dark Mode CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=2.5">
</head>
<body>

    <!-- Top Navigation Bar -->
    <nav class="navbar">
        <a href="index.php" class="brand-logo">
            <span class="logo-icon">B</span>
            <span>BAROK<span style="color: var(--accent-gold);">.</span></span>
        </a>

        <ul class="nav-links">
            <li><a href="#" class="nav-link"><?= __('nav_home') ?></a></li>
            <li><a href="#services" class="nav-link"><?= __('nav_services') ?></a></li>
            <li><a href="#portfolio" class="nav-link"><?= __('nav_portfolio') ?></a></li>
            <li><a href="#reviews" class="nav-link"><?= __('nav_testimonials') ?></a></li>
            <li><a href="booking.php" class="nav-link"><?= __('nav_book_btn') ?></a></li>
        </ul>

        <div class="nav-actions">
            <!-- Language Switcher Pill -->
            <?= renderLangSwitcher() ?>

            <a href="booking.php" class="btn btn-primary btn-sm">
                <span><?= __('nav_book_btn') ?></span>
                <span>→</span>
            </a>
            <button class="mobile-menu-toggle" aria-label="Toggle navigation">☰</button>
        </div>
    </nav>

    <!-- Cinematic Hero Section -->
    <header class="hero-section">
        <!-- Ambient Glow Elements -->
        <div class="hero-ambient-glow glow-1"></div>
        <div class="hero-ambient-glow glow-2"></div>

        <div class="container hero-container">
            <!-- Animated Floating Badge -->
            <div class="hero-badge animate-fade-in-down">
                <span class="badge-sparkle">✨</span>
                <span class="badge-text"><?= __('hero_badge') ?></span>
                <span class="pulse-dot"></span>
            </div>

            <!-- Luxury Hero Brand Typography -->
            <h1 class="hero-title animate-slide-up-1">
                <span class="hero-brand-name">BAROK PRODUCTION</span>
            </h1>

            <!-- Compelling Bilingual Tagline -->
            <div class="hero-tagline-wrap animate-slide-up-2">
                <h2 class="hero-tagline"><?= __('hero_tagline') ?></h2>
                <p class="hero-tagline-sub">
                    <?= __('hero_tagline_sub') ?>
                </p>
            </div>

            <!-- Prominent Call to Action Buttons -->
            <div class="hero-cta animate-slide-up-3">
                <a href="booking.php" class="btn btn-primary hero-btn-book glow-btn">
                    <span><?= __('hero_cta_book') ?></span>
                    <span class="btn-icon-animated">⚡</span>
                </a>
                <a href="#portfolio" class="btn btn-glass hero-btn-explore">
                    <span><?= __('hero_cta_explore') ?></span>
                    <span class="btn-arrow-down">↓</span>
                </a>
            </div>

            <!-- Hero Video Reel Preview -->
            <div class="hero-showreel-preview glass-panel animate-slide-up-4">
                <img src="<?= htmlspecialchars($heroBgImage) ?>" alt="BAROK PRODUCTION Cinema Camera Rig">
                <div class="play-button-overlay pulse-play" onclick="openHeroVideo()" title="<?= __('hero_watch_showreel') ?>">
                    ▶
                </div>
            </div>

            <!-- Stats Counter Grid -->
            <div class="stats-grid animate-slide-up-4">
                <div class="glass-panel stat-box">
                    <span class="stat-number gradient-text"><?= htmlspecialchars((string)$statShoots) ?></span>
                    <span class="stat-label"><?= __('stat_shoots_label') ?></span>
                </div>
                <div class="glass-panel stat-box">
                    <span class="stat-number gradient-text"><?= htmlspecialchars((string)$statRating) ?></span>
                    <span class="stat-label"><?= __('stat_rating_label') ?></span>
                </div>
                <div class="glass-panel stat-box">
                    <span class="stat-number gradient-text"><?= htmlspecialchars((string)$statAwards) ?></span>
                    <span class="stat-label"><?= __('stat_awards_label') ?></span>
                </div>
                <div class="glass-panel stat-box">
                    <span class="stat-number gradient-text"><?= htmlspecialchars((string)$statHdr) ?></span>
                    <span class="stat-label"><?= __('stat_hdr_label') ?></span>
                </div>
            </div>
        </div>
    </header>

    <!-- About Section -->
    <section id="about" style="padding: 60px 0 100px 0;">
        <div class="container">
            <div class="glass-panel" style="padding: 60px 40px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px; align-items: center;">
                    <div>
                        <span class="section-tag"><?= __('about_tag') ?></span>
                        <h2 style="font-size: 2.4rem; margin-bottom: 20px;">
                            <?= __('about_title_1') ?> <br><span class="gradient-text"><?= __('about_title_2') ?></span>
                        </h2>
                        <p style="margin-bottom: 20px;">
                            <?= __('about_desc_1') ?>
                        </p>
                        <p style="margin-bottom: 30px;">
                            <?= __('about_desc_2') ?>
                        </p>
                        <div style="display: flex; gap: 24px; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="color: var(--accent-gold); font-size: 1.2rem;">✦</span>
                                <span style="font-weight: 600;"><?= __('about_feat_1') ?></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="color: var(--accent-gold); font-size: 1.2rem;">✦</span>
                                <span style="font-weight: 600;"><?= __('about_feat_2') ?></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="color: var(--accent-gold); font-size: 1.2rem;">✦</span>
                                <span style="font-weight: 600;"><?= __('about_feat_3') ?></span>
                            </div>
                        </div>
                    </div>
                    <div style="position: relative;">
                        <div class="glass-panel" style="border-radius: var(--border-radius-lg); overflow: hidden; transform: rotate(-1deg);">
                            <img src="https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&w=1000&q=80" alt="Behind the Scenes at Barok Studio" style="width: 100%; height: 380px; object-fit: cover; display: block;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section id="portfolio" style="padding: 60px 0 100px 0;">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><?= __('portfolio_tag') ?></span>
                <h2 class="section-title"><?= __('portfolio_title') ?></h2>
                <p><?= __('portfolio_subtitle') ?></p>
            </div>

            <!-- Dynamic Portfolio Filter Buttons -->
            <?php
            $portfolioCatIcons = [
                'music-video'   => '🎬',
                'wedding'       => '💍',
                'commercial'    => '📺',
                'fashion-model' => '👗',
                'birthday'      => '🎂',
                'baby-shower'   => '👶',
                'tiktok-reels'  => '📱',
                'youtube'       => '▶️',
                'podcast'       => '🎙️',
                'event'         => '🎪',
                'product-photo' => '📦',
                'portrait'      => '👤',
                'drone'         => '🛸',
            ];
            $preferredOrder = [
                'music-video', 'wedding', 'commercial', 'fashion-model', 'birthday',
                'baby-shower', 'tiktok-reels', 'youtube', 'podcast', 'event',
                'product-photo', 'portrait', 'drone'
            ];
            $existingCategories = array_values(array_unique(array_filter(array_column($portfolioItems, 'category'))));
            $displayCategories = array_values(array_intersect($preferredOrder, $existingCategories));
            foreach ($existingCategories as $ec) {
                if (!in_array($ec, $displayCategories, true)) {
                    $displayCategories[] = $ec;
                }
            }
            ?>
            <div class="portfolio-filter-tabs">
                <button class="portfolio-filter-btn active" data-filter="all">
                    <span>✦</span>
                    <span><?= __('cat_all') ?></span>
                </button>
                <?php foreach ($displayCategories as $catSlug): 
                    $catKey = 'cat_' . str_replace('-', '_', $catSlug);
                    $catLabel = __($catKey);
                    if ($catLabel === $catKey) {
                        $catLabel = ucwords(str_replace('-', ' ', $catSlug));
                    }
                    $catIcon = $portfolioCatIcons[$catSlug] ?? '✦';
                ?>
                    <button class="portfolio-filter-btn" data-filter="<?= htmlspecialchars($catSlug) ?>">
                        <span><?= $catIcon ?></span>
                        <span><?= htmlspecialchars($catLabel) ?></span>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Portfolio Grid -->
            <div class="portfolio-grid">
                <?php if (empty($portfolioItems)): ?>
                    <p style="text-align: center; grid-column: 1/-1;"><?= __('no_portfolio') ?></p>
                <?php else: ?>
                    <?php foreach ($portfolioItems as $item): ?>
                        <div class="portfolio-card glass-panel" 
                             data-category="<?= htmlspecialchars($item['category']) ?>"
                             data-title="<?= htmlspecialchars($item['title']) ?>"
                             data-type="<?= htmlspecialchars($item['media_type']) ?>"
                             data-media="<?= htmlspecialchars($item['media_url'] ?: $item['thumbnail_url']) ?>"
                             data-desc="<?= htmlspecialchars($item['description'] ?? '') ?>"
                             data-client="<?= htmlspecialchars($item['client_name'] ?? '') ?>">
                            
                            <div class="portfolio-thumb">
                                <img src="<?= htmlspecialchars($item['thumbnail_url']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" loading="lazy">
                                <div class="portfolio-media-indicator">
                                    <?= $item['media_type'] === 'video' ? '▶' : '⌕' ?>
                                </div>
                                <div class="portfolio-overlay">
                                    <span class="portfolio-cat-badge">
                                        <?php 
                                            $badgeKey = 'cat_' . str_replace('-', '_', $item['category']);
                                            $badgeLabel = __($badgeKey);
                                            if ($badgeLabel === $badgeKey) {
                                                $badgeLabel = strtoupper(str_replace('-', ' ', $item['category']));
                                            }
                                            echo htmlspecialchars($badgeLabel);
                                        ?>
                                    </span>
                                    <h3 class="portfolio-project-title"><?= htmlspecialchars($item['title']) ?></h3>
                                    <?php if (!empty($item['client_name'])): ?>
                                        <span class="portfolio-project-client"><?= __('client_label') ?>: <?= htmlspecialchars($item['client_name']) ?> • <?= $item['project_year'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Services & Pricing Section -->
    <section id="services" style="padding: 60px 0 100px 0;">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><?= __('services_tag') ?></span>
                <h2 class="section-title"><?= __('services_title') ?></h2>
                <p><?= __('services_subtitle') ?></p>
            </div>

            <!-- Service Type Filter Tabs -->
            <div class="services-filter-tabs">
                <button class="service-filter-btn active" data-service-type="all"><?= __('service_tab_all') ?></button>
                <button class="service-filter-btn" data-service-type="photo">📷 <?= __('service_tab_photo') ?></button>
                <button class="service-filter-btn" data-service-type="video">🎬 <?= __('service_tab_video') ?></button>
                <button class="service-filter-btn" data-service-type="both">📷🎬 <?= __('service_tab_both') ?></button>
            </div>

            <div class="services-grid">
                <?php foreach ($services as $service): 
                    $features = json_decode($service['features'] ?? '[]', true) ?: [];
                    $svcType = $service['service_type'] ?? 'photo';
                    $typeIcon = $svcType === 'video' ? '🎬' : ($svcType === 'both' ? '📷🎬' : '📷');
                ?>
                    <div class="glass-panel service-card <?= $service['is_featured'] ? 'featured' : '' ?>" data-service-type="<?= htmlspecialchars($svcType) ?>">
                        <?php if ($service['is_featured']): ?>
                            <span class="featured-pill"><?= __('service_popular') ?></span>
                        <?php endif; ?>

                        <div class="service-icon-box">
                            <?= $typeIcon ?>
                        </div>

                        <span style="font-size: 0.8rem; text-transform: uppercase; color: var(--accent-gold); font-weight: 700; letter-spacing: 1px;">
                            <?= htmlspecialchars($service['category']) ?>
                        </span>

                        <h3 style="font-size: 1.35rem; margin-top: 6px;"><?= htmlspecialchars($service['title']) ?></h3>
                        
                        <p style="font-size: 0.88rem; margin-top: 10px; color: var(--text-secondary);">
                            <?= htmlspecialchars($service['short_desc']) ?>
                        </p>

                        <div class="service-price-tag">
                            <span class="amount"><?= formatPrice((float)$service['price']) ?></span>
                            <span class="period"><?= __('service_session') ?> (<?= $service['duration_hours'] ?><?= __('service_hours') ?>)</span>
                        </div>

                        <ul class="service-feature-list">
                            <?php foreach ($features as $feature): ?>
                                <li>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    <span><?= htmlspecialchars($feature) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <a href="booking.php?package=<?= $service['id'] ?>" class="btn <?= $service['is_featured'] ? 'btn-primary' : 'btn-glass' ?>" style="width: 100%;">
                            <span><?= __('service_book_btn') ?></span>
                            <span>→</span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Client Reviews Section -->
    <section id="reviews" style="padding: 60px 0 100px 0;">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><?= __('reviews_tag') ?></span>
                <h2 class="section-title"><?= __('reviews_title') ?></h2>
            </div>

            <?php if (!empty($testimonials)): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;" id="reviewsGrid">
                <?php foreach ($testimonials as $t):
                    $stars = (int)($t['rating'] ?? 5);
                    $initials = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', trim($t['client_name'])), 0, 2))));
                ?>
                <div class="glass-panel" style="padding: 32px 28px;">
                    <div style="color: var(--accent-gold); font-size: 1.2rem; margin-bottom: 14px;">
                        <?= str_repeat('★', $stars) . str_repeat('☆', 5 - $stars) ?>
                    </div>
                    <p style="font-size: 1rem; font-style: italic; margin-bottom: 20px;">
                        "<?= htmlspecialchars($t['review_text']) ?>"
                    </p>
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--bg-surface-elevated); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--accent-gold);">
                            <?= htmlspecialchars($initials) ?>
                        </div>
                        <div>
                            <h4 style="font-size: 0.95rem;"><?= htmlspecialchars($t['client_name']) ?></h4>
                            <span style="font-size: 0.8rem; color: var(--text-muted);"><?= htmlspecialchars($t['client_role'] ?? '') ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="text-align: center; color: var(--text-muted); padding: 40px 0;"><?= __('no_reviews_yet') ?></p>
            <?php endif; ?>

            <!-- Leave a Review CTA -->
            <div style="text-align: center; margin-top: 40px;">
                <button class="btn btn-primary" onclick="openReviewModal()" style="gap: 10px;">
                    <span>💬</span>
                    <span><?= __('reviews_leave_btn') ?></span>
                </button>
            </div>
        </div>
    </section>

    <!-- Review Submission Modal -->
    <div id="reviewModal" style="display:none; position:fixed; inset:0; z-index:9000; background:rgba(0,0,0,0.7); backdrop-filter:blur(6px); align-items:center; justify-content:center; padding:20px;">
        <div class="glass-panel" style="max-width:560px; width:100%; padding:40px 36px; position:relative; border-radius:var(--border-radius-md); max-height:90vh; overflow-y:auto;">
            <button onclick="closeReviewModal()" style="position:absolute; top:16px; right:20px; background:none; border:none; color:var(--text-muted); font-size:1.5rem; cursor:pointer; line-height:1;">✕</button>

            <h3 style="font-size:1.35rem; margin-bottom:6px;"><?= __('modal_review_title') ?></h3>
            <p style="color:var(--text-muted); font-size:0.88rem; margin-bottom:28px;"><?= __('modal_review_desc') ?></p>

            <form id="reviewForm" novalidate>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label"><?= __('review_field_name') ?> *</label>
                        <input type="text" id="rv_name" class="form-input" placeholder="<?= __('review_field_name_ph') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= __('review_field_role') ?></label>
                        <input type="text" id="rv_role" class="form-input" placeholder="<?= __('review_field_role_ph') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= __('review_field_project') ?></label>
                    <select id="rv_project" class="form-select">
                        <option value="">— <?= __('review_field_project_ph') ?> —</option>
                        <option value="Wedding Photography">Wedding Photography</option>
                        <option value="Music Video">Music Video</option>
                        <option value="Birthday Shoot">Birthday Shoot</option>
                        <option value="Corporate / Commercial">Corporate / Commercial</option>
                        <option value="TikTok / Reels">TikTok / Reels</option>
                        <option value="Podcast Production">Podcast Production</option>
                        <option value="Drone & Aerial">Drone & Aerial</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= __('review_field_rating') ?> *</label>
                    <div id="starRatingWidget" style="display:flex; gap:10px; font-size:2rem; cursor:pointer; margin-top:6px;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star-btn" data-val="<?= $i ?>" style="color:var(--text-muted); transition:color 0.2s;">★</span>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" id="rv_rating" value="0">
                </div>

                <div class="form-group">
                    <label class="form-label"><?= __('review_field_text') ?> *</label>
                    <textarea id="rv_text" class="form-textarea" rows="4" placeholder="<?= __('review_field_text_ph') ?>" required></textarea>
                </div>

                <p style="font-size:0.78rem; color:var(--text-muted); margin-bottom:16px;">
                    <?= __('review_moderation_note') ?>
                </p>

                <button type="submit" class="btn btn-primary" style="width:100%;" id="submitReviewBtn">
                    <span id="submitReviewLabel"><?= __('review_submit_btn') ?></span>
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Inquiry & Contact Section -->
    <section id="contact" style="padding: 60px 0 100px 0;">
        <div class="container">
            <div class="glass-panel" style="padding: 50px 40px; max-width: 820px; margin: 0 auto;">
                <div class="section-header" style="margin-bottom: 36px;">
                    <span class="section-tag"><?= __('contact_tag') ?></span>
                    <h2 class="section-title"><?= __('contact_title') ?></h2>
                    <p><?= __('contact_subtitle') ?></p>
                </div>

                <form id="contactForm">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
                        <div class="form-group">
                            <label class="form-label" for="contactName"><?= __('form_name') ?></label>
                            <input type="text" id="contactName" name="name" class="form-input" placeholder="<?= __('form_name_placeholder') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="contactEmail"><?= __('form_email') ?></label>
                            <input type="email" id="contactEmail" name="email" class="form-input" placeholder="<?= __('form_email_placeholder') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contactSubject"><?= __('form_subject') ?></label>
                        <input type="text" id="contactSubject" name="subject" class="form-input" placeholder="<?= __('form_subject_placeholder') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contactMessage"><?= __('form_message') ?></label>
                        <textarea id="contactMessage" name="message" class="form-textarea" placeholder="<?= __('form_message_placeholder') ?>" required></textarea>
                    </div>

                    <div style="text-align: center; margin-top: 10px;">
                        <button type="submit" class="btn btn-primary" style="padding: 14px 38px;">
                            <span><?= __('form_send_btn') ?></span>
                            <span>✈</span>
                        </button>
                    </div>

                    <div class="contact-direct">
                        <span class="contact-direct-label"><?= __('contact_direct_label') ?></span>
                        <a href="tel:<?= htmlspecialchars($appPhoneRaw) ?>" class="phone-link">📞 <?= htmlspecialchars($appPhone) ?></a>
                        <div class="social-links" style="margin-top: 0; justify-content: center;">
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
                </form>
            </div>
        </div>
    </section>

    <!-- Lightbox Modal Component -->
    <div id="portfolioLightbox" class="lightbox-modal">
        <div class="lightbox-content-wrapper">
            <button class="lightbox-close-btn" aria-label="Close modal">✕</button>
            <div class="lightbox-media-container"></div>
            <div class="glass-panel lightbox-caption">
                <h3 class="lightbox-title" style="font-size: 1.3rem; margin-bottom: 4px;"></h3>
                <span class="lightbox-client" style="color: var(--accent-gold); font-size: 0.85rem; display: block; margin-bottom: 8px;"></span>
                <p class="lightbox-desc" style="font-size: 0.9rem;"></p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <a href="index.php" class="brand-logo" style="margin-bottom: 16px; display: inline-flex;">
                        <span class="logo-icon">B</span>
                        <span>BAROK<span style="color: var(--accent-gold);">.</span></span>
                    </a>
                    <p style="font-size: 0.9rem; max-width: 320px; margin-bottom: 20px;">
                        <?= __('footer_desc') ?>
                    </p>
                    <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.8;">
                        📍 <?= APP_LOCATION ?><br>
                        <a href="tel:<?= htmlspecialchars($appPhoneRaw) ?>" class="phone-link" style="margin-top:4px; display:inline-flex;">📞 <?= htmlspecialchars($appPhone) ?></a><br>
                        ✉️ <a href="mailto:<?= APP_EMAIL ?>" style="color: var(--text-muted); text-decoration:none;"><?= APP_EMAIL ?></a>
                    </p>
                    <!-- Social Media Links -->
                    <div class="social-links">
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

                <div class="footer-col">
                    <h4><?= __('footer_nav_title') ?></h4>
                    <ul class="footer-links">
                        <li><a href="#about"><?= __('nav_about') ?></a></li>
                        <li><a href="#portfolio"><?= __('nav_portfolio') ?></a></li>
                        <li><a href="#services"><?= __('nav_services') ?></a></li>
                        <li><a href="booking.php"><?= __('nav_book_btn') ?></a></li>
                        <li><a href="booking-status.php"><?= __('nav_track') ?></a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4><?= __('footer_categories_title') ?></h4>
                    <ul class="footer-links">
                        <li><a href="#portfolio"><?= __('cat_music_video') ?></a></li>
                        <li><a href="#portfolio"><?= __('cat_commercial') ?></a></li>
                        <li><a href="#portfolio"><?= __('cat_event') ?></a></li>
                        <li><a href="#portfolio"><?= __('cat_portrait') ?></a></li>
                        <li><a href="#portfolio"><?= __('cat_drone') ?></a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4><?= __('footer_hours_title') ?></h4>
                    <p style="font-size: 0.88rem; line-height: 1.6; color: var(--text-muted); margin-bottom: 14px;">
                        <?= __('footer_hours_desc') ?>
                    </p>
                    <a href="booking-status.php" class="btn btn-glass btn-sm" style="width: 100%;">
                        <span><?= __('footer_track_btn') ?></span>
                        <span>🔍</span>
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
    <script>
    // ── Hero Video Reel Lightbox ─────────────────────────────────────────────
    function openHeroVideo() {
        const videoUrl = <?= json_encode($heroVideoUrl) ?>;
        const lightbox = document.getElementById('portfolioLightbox');
        if (!lightbox) return;

        const container = lightbox.querySelector('.lightbox-media-container');
        const titleEl = lightbox.querySelector('.lightbox-title');
        const descEl = lightbox.querySelector('.lightbox-desc');
        const clientEl = lightbox.querySelector('.lightbox-client');

        container.innerHTML = '';
        const iframe = document.createElement('iframe');
        iframe.src = videoUrl.includes('?') ? videoUrl + '&autoplay=1' : videoUrl + '?autoplay=1';
        iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
        iframe.allowFullscreen = true;
        container.appendChild(iframe);

        if (titleEl) titleEl.textContent = 'BAROK PRODUCTION — Official Showreel';
        if (descEl) descEl.textContent = 'Visionary 4K HDR Cinematography, Sound Design & Creative Direction';
        if (clientEl) clientEl.textContent = 'Studio Production';

        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // ── Review Modal ─────────────────────────────────────────────────────────
    function openReviewModal() {
        const m = document.getElementById('reviewModal');
        m.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeReviewModal() {
        const m = document.getElementById('reviewModal');
        m.style.display = 'none';
        document.body.style.overflow = '';
    }
    document.getElementById('reviewModal').addEventListener('click', function(e) {
        if (e.target === this) closeReviewModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeReviewModal();
    });

    // ── Star rating widget ────────────────────────────────────────────────────
    const stars = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rv_rating');
    stars.forEach(star => {
        star.addEventListener('mouseover', function() {
            const val = parseInt(this.dataset.val);
            stars.forEach((s, i) => {
                s.style.color = i < val ? 'var(--accent-gold)' : 'var(--text-muted)';
            });
        });
        star.addEventListener('click', function() {
            ratingInput.value = this.dataset.val;
            stars.forEach((s, i) => {
                s.style.color = i < parseInt(ratingInput.value) ? 'var(--accent-gold)' : 'var(--text-muted)';
            });
        });
    });
    document.getElementById('starRatingWidget').addEventListener('mouseleave', function() {
        const selected = parseInt(ratingInput.value);
        stars.forEach((s, i) => {
            s.style.color = i < selected ? 'var(--accent-gold)' : 'var(--text-muted)';
        });
    });

    // ── Review form AJAX submit ───────────────────────────────────────────────
    document.getElementById('reviewForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const name = document.getElementById('rv_name').value.trim();
        const role = document.getElementById('rv_role').value.trim();
        const project = document.getElementById('rv_project').value;
        const rating = parseInt(ratingInput.value);
        const text = document.getElementById('rv_text').value.trim();

        if (!name) { showToast('Please enter your name.'); return; }
        if (rating < 1 || rating > 5) { showToast('Please select a star rating.'); return; }
        if (!text) { showToast('Please write a short review.'); return; }

        const btn = document.getElementById('submitReviewBtn');
        const label = document.getElementById('submitReviewLabel');
        btn.disabled = true;
        label.textContent = 'Submitting…';

        try {
            const res = await fetch('api/testimonials.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ client_name: name, client_role: role, project_type: project, rating: rating, review_text: text })
            });
            const data = await res.json();
            if (data.success) {
                closeReviewModal();
                document.getElementById('reviewForm').reset();
                ratingInput.value = 0;
                stars.forEach(s => s.style.color = 'var(--text-muted)');
                showToast('✅ Thank you! Your review is pending moderation and will appear soon.');
            } else {
                showToast('Error: ' + (data.message || 'Submission failed. Please try again.'));
            }
        } catch (err) {
            showToast('Network error. Please check your connection and try again.');
        } finally {
            btn.disabled = false;
            label.textContent = '<?= __('review_submit_btn') ?>';
        }
    });

    function showToast(msg) {
        let t = document.getElementById('globalToast');
        if (!t) {
            t = document.createElement('div');
            t.id = 'globalToast';
            t.style.cssText = 'position:fixed;bottom:32px;left:50%;transform:translateX(-50%);background:rgba(30,30,40,0.97);border:1px solid rgba(255,255,255,0.15);color:#fff;padding:14px 28px;border-radius:12px;font-size:0.9rem;z-index:99999;transition:opacity 0.4s;max-width:90vw;text-align:center;';
            document.body.appendChild(t);
        }
        t.textContent = msg;
        t.style.opacity = '1';
        clearTimeout(t._timer);
        t._timer = setTimeout(() => { t.style.opacity = '0'; }, 4000);
    }
    </script>
</body>
</html>
