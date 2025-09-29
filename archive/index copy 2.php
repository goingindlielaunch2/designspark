<?php 
require_once 'stripe-config.php';
require_once 'report-storage.php';
require_once 'review-functions.php';

// Get the actual count of reports this week, with a minimum of 1 for better UX
$reportsThisWeek = max(1, getReportsThisWeek());

// Get real review stats from database
$reviewStats = getReviewStats();

// Get featured reviews for homepage display
$featuredReviews = getFeaturedReviews(3);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <!-- SEO Meta Tags -->
    <title>DesignSpark: Website Fixes & Development - WordPress, Shopify, Wix Expert</title>
    <meta name="description" content="Professional website fixes, development, and design services. Expert help for WordPress, Shopify, Wix, Squarespace sites. Fast 48-hour turnaround, monthly support plans available." />
    <meta name="keywords" content="website fixes, website development, WordPress fixes, Shopify development, Wix support, website design, web development, website maintenance, responsive design, website optimization, UX improvements, website evaluation, emergency website fixes, monthly website support" />
    <meta name="author" content="DesignSpark" />
    <meta name="robots" content="index, follow" />
    
    <!-- Open Graph Meta Tags for Social Media -->
    <meta property="og:title" content="DesignSpark: Professional Website Fixes & Development Services" />
    <meta property="og:description" content="Get expert website fixes and development for WordPress, Shopify, Wix, and more. Fast 48-hour turnaround with satisfaction guarantee." />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://withdesignspark.com" />
    <meta property="og:site_name" content="DesignSpark" />
    <meta property="og:locale" content="en_US" />
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="DesignSpark: Website Fixes & Development Expert" />
    <meta name="twitter:description" content="Professional website fixes for WordPress, Shopify, Wix & more. 48-hour turnaround guaranteed." />
    
    <!-- Additional SEO Meta Tags -->
    <meta name="theme-color" content="#6366f1" />
    <meta name="msapplication-TileColor" content="#6366f1" />
    <link rel="canonical" href="https://withdesignspark.com" />
    
    <!-- Structured Data for Local Business -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ProfessionalService",
      "name": "DesignSpark",
      "description": "Professional website fixes, development, and design services for WordPress, Shopify, Wix, and custom websites.",
      "url": "https://withdesignspark.com",
      "areaServed": "Worldwide",
      "serviceType": "Website Development and Maintenance",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Holladay",
        "addressRegion": "Utah",
        "addressCountry": "US"
      },
      "offers": [
        {
          "@type": "Offer",
          "name": "Quick Fix",
          "price": "99",
          "priceCurrency": "USD",
          "description": "Fix one specific website issue with 48-hour turnaround"
        },
        {
          "@type": "Offer",
          "name": "Monthly Club",
          "price": "1999",
          "priceCurrency": "USD",
          "description": "Unlimited website fixes and maintenance with monthly subscription"
        },
        {
          "@type": "Offer",
          "name": "Website Overhaul",
          "price": "3999",
          "priceCurrency": "USD",
          "description": "Complete website redesign and optimization"
        }
      ]
    }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              primary: "#6366f1",
              secondary: "#8b5cf6",
              accent: "#06b6d4",
            },
          },
        },
      };
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="main.css" />
  </head>

  <body class="bg-gray-50">
    <header class="w-full bg-white shadow-sm fixed top-0 left-0 z-40">
      <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-20"
      >
        <a href="/" class="flex items-center space-x-3 group">
          <svg
            class="w-10 h-10 text-primary"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M13 10V3L4 14h7v7l9-11h-7z"
            ></path>
          </svg>
          <span
            class="text-lg md:text-lg font-bold text-gray-900 group-hover:text-primary transition-colors">
            DesignSpark
          </span>
        </a>
        <nav class="flex items-center space-x-4">
          <a
            href="#"
            class="hidden md-block text-gray-700 hover:text-primary font-medium px-4 py-2 rounded transition-colors"
            >Login</a
          >
          <a
            class="bg-gradient-to-r from-primary to-secondary text-white px-5 py-2 rounded-xl font-semibold shadow hover:scale-105 transition-transform"
            href="https://calendly.com/david-withdesignspark/30min"
            target="_blank"
            >Book a Call</a
          >
          <a
            href="#pricing"
            class="hidden md-block text-primary hover:text-secondary font-semibold px-4 py-2 rounded transition-colors underline underline-offset-4 hover:no-underline"
            >See Pricing</a
          >
        </nav>
      </div>
    </header>

    <div class="h-20"></div>

    <section
      class="bg-gray-50 min-h-screen flex items-center justify-center px-4 py-20"
    >
      <div class="max-w-2xl mx-auto text-center">
        <div class="animate-on-scroll">
          <!-- Booking indicator -->
          <div class="mb-6 animate-on-scroll">
            <div
              class="bg-green-50 border border-green-200 rounded-full px-4 py-2 inline-flex items-center space-x-2 text-green-700 font-medium text-sm"
            >
              <div
                class="w-2 h-2 bg-green-500 rounded-full animate-pulse"
              ></div>
              <span>Booking for July</span>
            </div>
          </div>
          
                <h1 class="text-4xl md:text-7xl font-bold mb-6 leading-tight text-gray-900">
                    <span
                        class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">Sites engineered for success</span>
                </h1>

<p class="text-lg md:text-lg mb-12 text-gray-600 max-w-3xl mx-auto leading-relaxed animate-on-scroll delay-1">
Elevate your brand with a user-friendly website engineered to capture attention and maximize conversions. From complete website designs to small development fixes, I've got you covered.</p>
          </p>
          <div
            class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12 animate-on-scroll delay-2"
          >
            <div class="relative">
              <button
                onclick="scrollToPricing()"
                class="bg-gradient-to-r from-primary to-secondary text-white px-10 py-4 rounded-full text-lg font-semibold inline-flex items-center space-x-2 hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl relative overflow-hidden group active:scale-95 active:brightness-90"
              >
                <span class="relative z-10">Get Started Now</span>
                <svg
                  class="w-5 h-5 relative z-10"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 7l5 5m0 0l-5 5m5-5H6"
                  ></path>
                </svg>
                <div
                  class="absolute inset-0 bg-gradient-to-r from-secondary to-primary opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                ></div>
              </button>
            </div>
            <div class="relative">
              <button
                onclick="scrollToEvaluation()"
                class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-10 py-4 rounded-full text-lg font-semibold inline-flex items-center space-x-2 hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl active:scale-95 active:brightness-90"
              >
                <span>Free Website Evaluation</span>
                <svg
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                  ></path>
                </svg>
              </button>
              <div
                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse"
              >
                🔥 Hot
              </div>
            </div>
          </div>

          <div class="mb-8 animate-on-scroll delay-3">
            <div
              class="bg-green-50 border border-green-200 rounded-full px-6 py-3 inline-flex items-center space-x-3 text-green-700 font-medium"
            >
              <div
                class="w-3 h-3 bg-green-500 rounded-full animate-pulse"
              ></div>
              <span class="text-sm"
                >🚀 <span id="live-counter"><?php echo $reportsThisWeek; ?></span> websites evaluated this
                week</span
              >
            </div>
          </div>

          <div
            class="flex flex-col items-center space-y-4 animate-on-scroll delay-4"
          >
            <div class="flex items-center space-x-4">
              <div class="flex -space-x-2">
                <div
                  class="w-12 h-12 rounded-full border-3 border-gray-200 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm shadow-md"
                >
                  SM
                </div>
                <div
                  class="w-12 h-12 rounded-full border-3 border-gray-200 bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center text-white font-bold text-sm shadow-md"
                >
                  MJ
                </div>
                <div
                  class="w-12 h-12 rounded-full border-3 border-gray-200 bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center text-white font-bold text-sm shadow-md"
                >
                  LC
                </div>
                <div
                  class="w-12 h-12 rounded-full border-3 border-gray-200 bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white font-bold text-sm shadow-md"
                >
                  DK
                </div>
                <div
                  class="w-12 h-12 rounded-full border-3 border-gray-200 bg-gradient-to-br from-cyan-400 to-teal-500 flex items-center justify-center text-white font-bold text-sm shadow-md"
                >
                  AR
                </div>
              </div>

              <div class="reviews flex flex-col items-start">
                <div class="flex text-yellow-400 mb-1">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    ></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    ></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    ></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    ></path>
                  </svg>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                      d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                    ></path>
                  </svg>
                </div>
                <div class="text-gray-700 text-sm font-medium">
                  <?php echo $reviewStats['average']; ?>/5 from <?php echo $reviewStats['count']; ?>+ customers
                </div>
              </div>
            </div>
            
            <button 
              onclick="scrollToEvaluation()"
              class="btn-eval text-primary hover:text-secondary transition-colors text-sm font-medium underline underline-offset-4 hover:no-underline active:scale-95 active:text-secondary"
            >
              Evaluate your site now →
            </button>
          </div>
        </div>
      </div>
    </section>

    <section id="website-evaluation" class="bg-slate-100 py-20 px-4">
      <div id="evaluation-tool-container" class="max-w-4xl mx-auto">
        </div>
    </section>

<section id="portfolio" class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 animate-on-scroll">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Portfolio</h2>
          <p class="text-xl text-gray-600">A selection of my recent work</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
          <div
            class="relative group cursor-pointer animate-on-scroll"
            onclick="openPortfolioModal('images/dental-website-design.jpg')"
          >
            <img
              src="images/dental-website-design.jpg"
              alt="Website Design - dental-website-design.jpg"
              class="rounded-2xl shadow-lg w-full h-64 object-cover transition-transform duration-300 group-hover:scale-105"
            />
          </div>
          <div
            class="relative group cursor-pointer animate-on-scroll delay-1"
            onclick="openPortfolioModal('images/gripsock-ecommerce-website-design.jpg')"
          >
            <img
              src="images/gripsock-ecommerce-website-design.jpg"
              alt="Website Design - gripsock-ecommerce-website-design.jpg"
              class="rounded-2xl shadow-lg w-full h-64 object-cover transition-transform duration-300 group-hover:scale-105"
            />
          </div>
          <div
            class="relative group cursor-pointer animate-on-scroll delay-2"
            onclick="openPortfolioModal('images/payment-website-design.jpg')"
          >
            <img
              src="images/payment-website-design.jpg"
              alt="Website Design - payment-website-design.jpg"
              class="rounded-2xl shadow-lg w-full h-64 object-cover transition-transform duration-300 group-hover:scale-105"
            />
          </div>
          <div
            class="relative group cursor-pointer animate-on-scroll"
            onclick="openPortfolioModal('images/skin-care-web-design.jpg')"
          >
            <img
              src="images/skin-care-web-design.jpg"
              alt="Website Design - skin-care-web-design.jpg"
              class="rounded-2xl shadow-lg w-full h-64 object-cover transition-transform duration-300 group-hover:scale-105"
            />
          </div>
          <div
            class="relative group cursor-pointer animate-on-scroll delay-1"
            onclick="openPortfolioModal('images/bloom-branding.jpg')"
          >
            <img
              src="images/bloom-branding.jpg"
              alt="Branding - bloom-branding.jpg"
              class="rounded-2xl shadow-lg w-full h-64 object-cover transition-transform duration-300 group-hover:scale-105"
            />
          </div>
          <div
            class="relative group cursor-pointer animate-on-scroll delay-2"
            onclick="openPortfolioModal('images/app-design.jpg')"
          >
            <img
              src="images/app-design.jpg"
              alt="Website Design - app-design.jpg"
              class="rounded-2xl shadow-lg w-full h-64 object-cover transition-transform duration-300 group-hover:scale-105"
            />
          </div>
        </div>
      </div>
      <div
        id="portfolioModal"
        class="fixed inset-0 z-50 pointer-events-none hidden flex items-center justify-center"
      >
        <div
          id="modalBg"
          class="absolute inset-0 bg-black bg-opacity-70 opacity-0 transition-opacity duration-300"
        ></div>
        <div
          id="modalContent"
          class="relative max-w-3xl w-full mx-4 opacity-0 scale-95 transition-all duration-300 transform"
        >
          <button
            onclick="closePortfolioModal()"
            class="absolute top-2 right-2 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-80 focus:outline-none z-10 active:scale-95 active:bg-opacity-90"
          >
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              ></path>
            </svg>
          </button>
          <img
            id="modalImage"
            src=""
            alt="Portfolio Large"
            class="rounded-2xl shadow-2xl w-full max-h-[80vh] object-contain bg-white"
          />
        </div>
      </div>
    </section>



    <section class="stats-gradient py-20 px-4 relative overflow-hidden">
      <div
        class="absolute inset-0 bg-gradient-to-br from-slate-800/50 to-slate-900/50"
      ></div>

      <div class="max-w-6xl mx-auto relative z-10">
        <div class="mt-8 text-center animate-on-scroll delay-4">
          <div
            class="max-w-5xl mx-auto bg-gradient-to-br from-white via-slate-100 to-slate-200 rounded-3xl px-10 py-10 mb-8 shadow-lg border border-slate-200"
          >
            <p
              class="text-2xl md:text-3xl font-semibold text-gray-800 mb-4 leading-snug"
            >
              Our team has deep expertise across the most popular website
              platforms and builders.
            </p>
            <p class="text-lg md:text-xl text-gray-600 mb-0 leading-relaxed">
              Whether your site is built on
              <span class="font-bold text-primary">WordPress</span>,
              <span class="font-bold text-green-600">Shopify</span>,
              <span class="font-bold text-yellow-600">Wix</span>,
              <span class="font-bold text-gray-700">Squarespace</span>,
              <span class="font-bold text-blue-600">Webflow</span>, or
              <span class="font-bold text-blue-500">Framer</span>, we know the
              ins and outs to deliver fast, reliable fixes and improvements.
            </p>
          </div>
          <p class="text-gray-300 mb-2">We work with these trusted platforms</p>
          <div class="flex flex-wrap justify-center items-center gap-4 sm:gap-6 md:gap-8 opacity-60">
            <!-- WordPress -->
            <div class="flex items-center space-x-2">
              <div class="w-5 h-5 bg-[#21759B] rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="white" class="bi bi-wordpress" viewBox="0 0 16 16">
                  <path d="M12.633 7.653c0-.848-.305-1.435-.566-1.892l-.08-.13c-.317-.51-.594-.958-.594-1.48 0-.63.478-1.218 1.152-1.218q.03 0 .058.003l.031.003A6.84 6.84 0 0 0 8 1.137 6.86 6.86 0 0 0 2.266 4.23c.16.005.313.009.442.009.717 0 1.828-.087 1.828-.087.37-.022.414.521.044.565 0 0-.371.044-.785.065l2.5 7.434 1.5-4.506-1.07-2.929c-.369-.022-.719-.065-.719-.065-.37-.022-.326-.588.043-.566 0 0 1.134.087 1.808.087.718 0 1.83-.087 1.83-.087.37-.022.413.522.043.566 0 0-.372.043-.785.065l2.48 7.377.684-2.287.054-.173c.27-.86.469-1.495.469-2.046zM1.137 8a6.86 6.86 0 0 0 3.868 6.176L1.73 5.206A6.8 6.8 0 0 0 1.137 8"/>
                  <path d="M6.061 14.583 8.121 8.6l2.109 5.78q.02.05.049.094a6.85 6.85 0 0 1-4.218.109m7.96-9.876q.046.328.047.706c0 .696-.13 1.479-.522 2.458l-2.096 6.06a6.86 6.86 0 0 0 2.572-9.224z"/>
                  <path fill-rule="evenodd" d="M0 8c0-4.411 3.589-8 8-8s8 3.589 8 8-3.59 8-8 8-8-3.589-8-8m.367 0c0 4.209 3.424 7.633 7.633 7.633S15.632 12.209 15.632 8C15.632 3.79 12.208.367 8 .367 3.79.367.367 3.79.367 8"/>
                </svg>
              </div>
              <span class="text-white font-semibold">WordPress</span>
            </div>
            <div class="w-2 h-2 bg-gray-400 rounded-full hidden sm:block"></div>
            
            <!-- Shopify -->
            <div class="flex items-center space-x-2">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="12" fill="#95BF47"/>
                <path d="M15.31 6.94c-.01-.22-.17-.39-.39-.39-.06 0-.12.01-.18.04-.02-.01-.04-.03-.06-.04-.85-.5-1.94-.35-2.56.35-.48.54-.77 1.33-.89 2.2-.82.25-1.4.42-1.41.43-.47.15-.48.16-.54.62-.05.34-1.25 9.68-1.25 9.68l9.36 1.76s-2.08-14.23-2.08-14.65zm-2.44.65c-.46.14-1.01.31-1.6.49.31-1.2.89-1.8 1.4-1.99.13.34.2.8.2 1.5zm-.83-1.99c.09 0 .18.02.27.09-.66.31-1.39 1.11-1.69 2.7-.37.11-.73.22-1.07.32.35-1.21 1.2-3.11 2.49-3.11zm.36 7.08s-.54-.28-1.2-.28c-.98 0-1.02.61-1.02.77 0 .84 2.19 1.16 2.19 3.13 0 1.55-.98 2.54-2.31 2.54-1.59 0-2.4-.99-2.4-.99l.43-1.41s.84.72 1.54.72c.45 0 .65-.36.65-.63 0-1.1-1.8-1.15-1.8-2.95 0-1.51 1.08-2.99 3.29-2.99.85 0 1.26.25 1.26.25z" fill="white"/>
              </svg>
              <span class="text-white font-semibold">Shopify</span>
            </div>
            <div class="w-2 h-2 bg-gray-400 rounded-full hidden sm:block"></div>
            
            <!-- Wix -->
            <div class="flex items-center space-x-2">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="12" fill="#FAAD4E"/>
                <path d="M6 8l2.5 8h1.5L12 10l2 6h1.5L18 8h-1.5l-1.5 6L13 8h-2l-2 6L7.5 8H6z" fill="#000"/>
              </svg>
              <span class="text-white font-semibold">Wix</span>
            </div>
            <div class="w-2 h-2 bg-gray-400 rounded-full hidden sm:block"></div>
            
            <!-- Squarespace -->
            <div class="flex items-center space-x-2">
<svg height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><path d="M10 20c5.523 0 10 -4.477 10 -10S15.523 0 10 0 0 4.477 0 10s4.477 10 10 10" fill="#000"/><path d="M6.142 9.526 9.607 6.041a2.218 2.218 0 0 1 3.149 0c0.208 0.219 0.199 0.574 -0.016 0.789a0.554 0.554 0 0 1 -0.788 0l-0.052 -0.061a1.108 1.108 0 0 0 -1.506 0.064l-3.543 3.563c-0.233 0.216 -0.586 0.216 -0.803 -0.002a0.562 0.562 0 0 1 0.094 -0.868m7.779 0.076a0.554 0.554 0 0 0 -0.863 0.094L9.607 13.168a1.11 1.11 0 0 1 -1.574 0l-0.016 -0.002a0.554 0.554 0 0 0 -0.787 0 0.562 0.562 0 0 0 0.089 0.865 2.218 2.218 0 0 0 3.076 -0.071l3.527 -3.566a0.562 0.562 0 0 0 0 -0.792M8.503 11.902q-0.05 0.032 -0.093 0.076a0.562 0.562 0 0 0 0 0.791c0.217 0.219 0.569 0.219 0.803 0.003l3.542 -3.564a1.109 1.109 0 0 1 1.574 0 1.123 1.123 0 0 1 0 1.584L10.971 14.187a1.11 1.11 0 0 0 1.575 0l2.572 -2.603c0.869 -0.874 0.869 -2.292 0 -3.167a2.219 2.219 0 0 0 -3.149 0zm-0.471 -0.318 3.527 -3.566a0.562 0.562 0 0 0 0 -0.792 0.554 0.554 0 0 0 -0.863 0.094L7.245 10.792a1.108 1.108 0 0 1 -1.574 0 1.123 1.123 0 0 1 0 -1.584L9.03 5.813a1.109 1.109 0 0 0 -1.575 0l-2.572 2.603a2.25 2.25 0 0 0 0 3.168 2.217 2.217 0 0 0 3.149 0" fill="#FFF"/></g></svg>
              <span class="text-white font-semibold">Squarespace</span>
            </div>
            <div class="w-2 h-2 bg-gray-400 rounded-full hidden sm:block"></div>
            
            <!-- Webflow -->
            <div class="flex items-center space-x-2">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="12" fill="#4353FF"/>
                <path d="M7 8h3l1 4 1-4h3l1 4 1-4h3l-2 8h-3l-1-4-1 4H9l-2-8z" fill="white"/>
              </svg>
              <span class="text-white font-semibold">Webflow</span>
            </div>
            <div class="w-2 h-2 bg-gray-400 rounded-full hidden sm:block"></div>
            
            <!-- Framer -->
            <div class="flex items-center space-x-2">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="12" fill="#0055FF"/>
                <path d="M8 6h8v4h-4l4 4H8v-4h4l-4-4z" fill="white"/>
              </svg>
              <span class="text-white font-semibold">Framer</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    

    <section class="py-20 px-4 bg-white">
      <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16 animate-on-scroll">
          <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
            Why Choose <span class="text-primary">DesignSpark?</span>
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Skip the headaches of inconsistent freelancers and overpriced agencies—DesignSpark offers unlimited design and development work for one predictable monthly cost, with turnaround times so quick you'll never look elsewhere.
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div class="text-center animate-on-scroll">
            <div
              class="w-20 h-20 bg-gradient-to-br from-blue-100 to-primary/20 rounded-2xl flex items-center justify-center mx-auto mb-6"
            >
              <svg
                class="w-10 h-10 text-primary"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 10V3L4 14h7v7l9-11h-7z"
                ></path>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">
              Lightning Fast
            </h3>
            <p class="text-gray-600 leading-relaxed">
              Most fixes completed within 48 hours. Emergency repairs can be
              done same-day. No more waiting weeks for simple fixes.
            </p>
          </div>

          <div class="text-center animate-on-scroll delay-1">
            <div
              class="w-20 h-20 bg-gradient-to-br from-green-100 to-green-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6"
            >
              <svg
                class="w-10 h-10 text-green-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 10V3L4 14h7v7l9-11h-7z"
                ></path>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">
              Love It Guarantee
            </h3>
            <p class="text-gray-600 leading-relaxed">
              If you're not satisfied, we'll work until you are. We want you to
              love the results.
            </p>
          </div>

          <div class="text-center animate-on-scroll delay-2">
            <div
              class="w-20 h-20 bg-gradient-to-br from-purple-100 to-purple-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6"
            >
              <svg
                class="w-10 h-10 text-purple-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12l2 2 4-4"
                ></path>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Expert Team</h3>
            <p class="text-gray-600 leading-relaxed">
              Our developer has 10+ years experience with every major platform.
              No problem is too complex for us to solve.
            </p>
          </div>

          <div class="text-center animate-on-scroll delay-3">
            <div
              class="w-20 h-20 bg-gradient-to-br from-cyan-100 to-cyan-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6"
            >
              <svg
                class="w-10 h-10 text-cyan-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                ></path>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Fair Pricing</h3>
            <p class="text-gray-600 leading-relaxed">
              No hidden fees or surprise charges. Transparent pricing with fixed
              rates so you know exactly what you're paying upfront.
            </p>
          </div>

          <div class="text-center animate-on-scroll delay-4">
            <div
              class="w-20 h-20 bg-gradient-to-br from-orange-100 to-orange-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6"
            >
              <svg
                class="w-10 h-10 text-orange-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                ></path>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">All Platforms</h3>
            <p class="text-gray-600 leading-relaxed">
              WordPress, Shopify, Wix, Squarespace, custom sites - we fix them
              all. One team for all your website needs.
            </p>
          </div>

          <div class="text-center animate-on-scroll delay-1">
            <div
              class="w-20 h-20 bg-gradient-to-br from-pink-100 to-pink-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6"
            >
              <svg
                class="w-10 h-10 text-pink-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                ></path>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">
              Personal Touch
            </h3>
            <p class="text-gray-600 leading-relaxed">
              You'll work directly with experienced developers, not junior
              staff. We treat your website like it's our own.
            </p>
          </div>
        </div>
      </div>
    </section>

    

    <!-- Featured Reviews Section -->
    <?php if (!empty($featuredReviews)): ?>
    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-on-scroll">
          <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">What Our Customers Say</h2>
<p class="text-xl text-gray-600 max-w-3xl mx-auto">Real feedback from website owners who I've worked with</p>
          <!-- <p class="text-xl text-gray-600 max-w-3xl mx-auto">Real feedback from website owners who improved their conversions with our detailed analysis</p> -->
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-<?php echo min(count($featuredReviews), 3); ?> gap-8 animate-on-scroll delay-1">
          <?php foreach ($featuredReviews as $index => $review): ?>
          <div class="bg-gradient-to-br from-white to-slate-50 rounded-2xl shadow-lg p-8 border border-slate-200 card-hover relative overflow-hidden group min-h-[320px] flex flex-col">
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-secondary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            
            <!-- Featured Badge -->
            <div class="flex justify-between items-start mb-6 relative z-10">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800 border border-yellow-200/50 shadow-sm">
                ⭐ Featured Review
              </span>
              <div class="text-right">
                <div class="text-yellow-400 text-xl mb-1 drop-shadow-sm">
                  <?php echo str_repeat('★', $review['rating']); ?>
                </div>
                <span class="text-sm text-slate-600 font-medium bg-slate-100 px-2 py-1 rounded-full"><?php echo $review['rating']; ?>/5</span>
              </div>
            </div>
            
            <!-- Review Text -->
            <div class="flex-1 flex items-center relative z-10">
              <blockquote class="text-slate-700 text-lg leading-relaxed font-medium text-center">
                "<?php echo htmlspecialchars($review['review_text']); ?>"
              </blockquote>
            </div>
            
            <!-- Customer Info - Always at Bottom -->
            <div class="flex items-center justify-center mt-8 pt-6 border-t border-slate-200 relative z-10">
              <div class="w-14 h-14 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                <?php echo strtoupper(substr($review['customer_name'], 0, 1)); ?>
              </div>
              <div class="ml-4 text-center">
                <p class="text-lg font-semibold text-slate-900"><?php echo htmlspecialchars($review['customer_name']); ?></p>
                <p class="text-sm text-slate-600 font-medium"><?php echo date('F Y', strtotime($review['created_at'])); ?></p>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        
        <!-- Call to Action -->
        <div class="text-center mt-12 animate-on-scroll delay-2">
          <p class="text-gray-600 mb-6">Join hundreds of satisfied website owners</p>
          <button 
            onclick="scrollToEvaluation()"
            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 transform hover:scale-105 shadow-lg"
          >
            Get Your Website Analysis Now
          </button>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <section id="pricing" class="py-20 px-4 bg-gray-50">
      <div class="container mx-auto">
        <div class="text-center mb-16 animate-on-scroll">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
            Simple, Transparent Pricing
          </h2>
          <p class="text-gray-600 max-w-2xl text-lg md:text-[1.25rem] mx-auto">
            Choose the plan that works best for your website needs, from
            one-time fixes to ongoing maintenance.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div
            class="bg-white rounded-xl shadow-lg overflow-hidden transition-transform hover:scale-105 flex flex-col h-full animate-on-scroll"
          >
            <div class="p-6 flex-1 flex flex-col">
              <h3 class="text-xl font-semibold text-gray-800 mb-4">
                Quick Fix
              </h3>
              <div class="flex items-baseline mb-6">
                <span class="text-4xl font-bold text-gray-800">$99</span>
                <span class="text-gray-500 ml-2">/ one-time</span>
              </div>
              <p class="text-gray-600 mb-6">
                Perfect for small issues that need immediate attention.
              </p>
              <ul class="space-y-3 mb-8">
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span>Fix 1 specific issue</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span>48-hour turnaround</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span>Direct communication with developer</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span>All major platforms supported</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span>For WordPress, Webflow, Shopify, Wix & More</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span>30-day guarantee</span>
                </li>
              </ul>
            </div>
            <div class="text-gray-600 mb-6 text-xs text-center flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Need more fixes than just one? Update qty on on Get Started page for more.</span>
            </div>
            <div class="px-6 pb-6 mt-auto">
              <a href="https://buy.stripe.com/6oU00jbcTeS35V8bJrbbG06" 
                class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 rounded-lg transition active:bg-gray-400 active:text-gray-900 active:scale-95 active:brightness-90 block text-center"
                style="transition: background 0.2s, color 0.2s, transform 0.1s"
              >
                Get Started - $99
              </a>
            </div>
          </div>

          <div
            class="bg-indigo-600 rounded-xl shadow-xl overflow-hidden transform scale-105 z-10 flex flex-col h-full animate-on-scroll delay-1"
          >
            <div class="bg-indigo-700 py-2">
              <p class="text-center text-white font-medium">MOST POPULAR</p>
            </div>
            <div class="p-6 flex-1 flex flex-col">
              <h3 class="text-xl font-semibold text-white mb-4">
                Monthly Club
              </h3>
              <div class="flex items-baseline mb-6">
                <span class="text-4xl font-bold text-white">$1999</span>
                <span class="text-indigo-200 ml-2">/ month</span>
              </div>
              <p class="text-indigo-100 mb-6">
                Ongoing design and development to support your brand.
              </p>
              <ul class="space-y-3 mb-8">
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-yellow-300 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span class="text-white">One task at a time</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-yellow-300 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span class="text-white">Average 48-hour delivery</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-yellow-300 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span class="text-white">Unlimited Site Fixes</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-yellow-300 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span class="text-white">Unlimited Brands</span>
                </li>

                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-yellow-300 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span class="text-white">Unlimited Stock Photos</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-yellow-300 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span class="text-white">New Webflow or Framer site development</span>
                </li>

                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-yellow-300 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span class="text-white">Pause or Cancel Anytime</span>
                </li>
              </ul>
              
              <!-- Monthly Club Guarantee -->
              <div class="mt-6 p-4 bg-white/10 rounded-xl border border-white/20">
                <div class="flex items-center justify-center mb-2">
                  <svg class="w-5 h-5 text-yellow-300 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <span class="text-white font-semibold text-sm md:text-lg">Monthly Club Guarantee</span>
                </div>
                <p class="text-white/90 text-sm md:text-lg text-center leading-relaxed">
                  Try it for a full week - love it or get 75% back instantly, no questions asked.
                </p>
              </div>
            </div>
            <div class="px-6 pb-6 mt-auto">
              <a href="https://buy.stripe.com/4gM7sLgxd6lx5V828RbbG05" 
                class="w-full bg-white hover:bg-gray-200 text-indigo-900 font-medium py-3 rounded-lg transition pulse active:bg-gray-300 active:text-indigo-800 active:scale-95 active:brightness-90 block text-center"
                style="transition: background 0.2s, color 0.2s, transform 0.1s"
              >
                Subscribe Now - $1999/mo
              </a>
            </div>
          </div>

          <div
            class="bg-white rounded-xl shadow-lg overflow-hidden transition-transform hover:scale-105 flex flex-col h-full animate-on-scroll delay-2"
          >
            <div class="p-6 flex-1 flex flex-col">
              <h3 class="text-xl font-semibold text-gray-800 mb-4">
                Website Overhaul
              </h3>
              <div class="flex items-baseline mb-6">
                <span class="text-4xl font-bold text-gray-800">$3999</span>
                <span class="text-gray-500 ml-2">/ one-time</span>
              </div>
              <p class="text-gray-600 mb-6">
                Complete redesign and optimization of your existing website.
              </p>
              <ul class="space-y-3 mb-8">
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span>Complete design refresh</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span>UX and conversion focused redesign</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span>Mobile-first responsive design</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span>Page speed optimization</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span>SEO foundation improvements</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span>Professional copywriting review</span>
                </li>
                <li class="flex items-start">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-green-500 mr-2 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span>Conversion rate optimization</span>
                </li>
               
              </ul>
              
              <!-- Website Overhaul Guarantee -->
              <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex items-center justify-center mb-2">
                  <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <span class="text-gray-800 font-semibold text-sm md:text-lg">Complete Overhaul Guarantee</span>
                </div>
                <p class="text-gray-600 text-sm md:text-lg text-center leading-relaxed">
                  We'll work until you're 100% satisfied with your new website design and performance.
                </p>
              </div>
            </div>
            <div class="px-6 pb-6 mt-auto">
              <a href="https://buy.stripe.com/6oU7sLcgX39lerE28RbbG04" 
                class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 rounded-lg transition active:bg-gray-400 active:text-gray-900 active:scale-95 active:brightness-90 block text-center"
                style="transition: background 0.2s, color 0.2s, transform 0.1s"
              >
                Get Started - $3999
              </a>
            </div>
          </div>
        </div>
        
        <!-- Trust Signals -->
        <div class="text-center mt-16 animate-on-scroll">
          <div class="flex justify-center items-center space-x-8 mb-6 flex-wrap gap-4">
            <div class="flex items-center text-gray-600">
              <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span class="font-medium">Secure Payment via Stripe</span>
            </div>
            <div class="flex items-center text-gray-600">
              <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span class="font-medium">Instant Access</span>
            </div>
            <div class="flex items-center text-gray-600">
              <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span class="font-medium">100% Satisfaction Guarantee</span>
            </div>
          </div>
          
          <p class="text-gray-600 mb-4">
            Trusted by <?php echo $reviewStats['count']; ?>+ businesses worldwide
          </p>
          
          <div class="flex justify-center items-center space-x-6 opacity-70">
            <span class="text-sm font-medium text-gray-500">Powered by</span>
            <svg class="h-6" viewBox="0 0 60 25" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20.7 14.5c0-1.4 1-2.2 2.7-2.2 1.3 0 2.9.4 4.1 1.1V9.8c-1.4-.6-2.8-.9-4.2-.9-3.5 0-6 2.2-6 5.6s2.4 5.6 6 5.6c1.4 0 2.8-.3 4.2-.9v-3.6c-1.2.7-2.8 1.1-4.1 1.1-1.7 0-2.7-.8-2.7-2.2z" fill="#6772E5"/>
              <path d="M32.1 20.1c-4.2 0-6.8-2.9-6.8-6.4 0-3.5 2.6-6.4 6.8-6.4s6.8 2.9 6.8 6.4c0 3.5-2.6 6.4-6.8 6.4zm0-3.4c1.7 0 2.9-1.3 2.9-3s-1.2-3-2.9-3-2.9 1.3-2.9 3 1.2 3 2.9 3z" fill="#6772E5"/>
            </svg>
          </div>
        </div>
      </div>
    </section>

    <section class="px-6 py-20 md:px-12 bg-white">
      <div class="max-w-4xl mx-auto">
        <h2
          id="faq"
          class="text-3xl md:text-4xl font-bold text-center mb-16 animate-on-scroll"
        >
          Frequently Asked Questions
        </h2>

        <div class="mb-12">
          <h3 class="text-2xl font-bold text-center mb-8 text-primary animate-on-scroll">
            Monthly Club
          </h3>
          <div class="space-y-4" id="faq-accordion-monthly">
            <div class="bg-gray-100 rounded-lg overflow-hidden animate-on-scroll">
              <button
                class="w-full px-6 py-4 text-left font-semibold flex justify-between items-center faq-toggle"
                data-faq="faq-1"
                aria-expanded="false"
              >
                <span>How fast will I receive my designs / tasks?</span>
                <svg
                  class="w-5 h-5 transform transition-transform"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  ></path>
                </svg>
              </button>
              <div
                class="px-6 pb-4 hidden text-gray-600 text-descriptive"
                id="faq-1"
              >
                Most requests are completed in two days or less. If you send
                over something more complex, it might take a bit more time, but
                I'll always keep you updated on the progress.
              </div>
            </div>
            <div
              class="bg-gray-100 rounded-lg overflow-hidden animate-on-scroll delay-1"
            >
              <button
                class="w-full px-6 py-4 text-left font-semibold flex justify-between items-center faq-toggle"
                data-faq="faq-4"
                aria-expanded="false"
              >
                <span>Is there a limit to how many requests I can make?</span>
                <svg
                  class="w-5 h-5 transform transition-transform"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  ></path>
                </svg>
              </button>
              <div
                class="px-6 pb-4 hidden text-gray-600 text-descriptive"
                id="faq-4"
              >
                Nope! Feel free to fill up your queue with as many design /
                development requests as you can think of. I'll get to work on
                them and deliver them back to you one by one.
              </div>
            </div>
            <div
              class="bg-gray-100 rounded-lg overflow-hidden animate-on-scroll delay-2"
            >
              <button
                class="w-full px-6 py-4 text-left font-semibold flex justify-between items-center faq-toggle"
                data-faq="faq-5"
                aria-expanded="false"
              >
                <span>How do Monthly site fixes work?</span>
                <svg
                  class="w-5 h-5 transform transition-transform"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  ></path>
                </svg>
              </button>
              <div
                class="px-6 pb-4 hidden text-gray-600 text-descriptive"
                id="faq-5"
              >
                The development process is straightforward. These are treated
                the same as 'design requests', As long as your request is
                supported by your website platform, DesignSpark will handle the
                fix and development for you. Just submit your fix request, and
                I'll take care of the rest.
              </div>
            </div>
            <div
              class="bg-gray-100 rounded-lg overflow-hidden animate-on-scroll delay-3"
            >
              <button
                class="w-full px-6 py-4 text-left font-semibold flex justify-between items-center faq-toggle"
                data-faq="faq-6"
                aria-expanded="false"
              >
                <span>How does webflow, framer development work?</span>
                <svg
                  class="w-5 h-5 transform transition-transform"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  ></path>
                </svg>
              </button>
              <div
                class="px-6 pb-4 hidden text-gray-600 text-descriptive"
                id="faq-6"
              >
                The development process is straightforward. These are treated
                the same as 'design requests'. As long as your request is
                supported by webflow or framer, DesignSpark will handle the
                development for you. Just submit your design request, and I'll
                take care of the rest. Once the design is ready, I'll build it
                out in Webflow or Framer, ensuring everything looks and works
                perfectly. You'll receive a link to review the live site before
                we finalize everything. And the website will be transferred to
                your account, so you have full ownership and control. At that
                point a new subscription will not be required to maintain the
                website.
              </div>
            </div>
            <div
              class="bg-gray-100 rounded-lg overflow-hidden animate-on-scroll delay-4"
            >
              <button
                class="w-full px-6 py-4 text-left font-semibold flex justify-between items-center faq-toggle"
                data-faq="faq-8"
                aria-expanded="false"
              >
                <span>How does the pause feature work?</span>
                <svg
                  class="w-5 h-5 transform transition-transform"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  ></path>
                </svg>
              </button>
              <div
                class="px-6 pb-4 hidden text-gray-600 text-descriptive"
                id="faq-8"
              >
                Got a quiet spell? I get it. You might not have enough design
                work for a full month straight. That's why you can pause your
                subscription anytime.
                <br /><br />
                Billing is based on a 31-day cycle. For example, if you use the
                service for 20 days and then hit pause, you'll still have 11
                days of service ready and waiting for whenever you need them
                next. No wasted days!
              </div>
            </div>
            <div class="bg-gray-100 rounded-lg overflow-hidden animate-on-scroll">
              <button
                class="w-full px-6 py-4 text-left font-semibold flex justify-between items-center faq-toggle"
                data-faq="faq-9"
                aria-expanded="false"
              >
                <span>How do you handle larger requests?</span>
                <svg
                  class="w-5 h-5 transform transition-transform"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  ></path>
                </svg>
              </button>
              <div
                class="px-6 pb-4 hidden text-gray-600 text-descriptive"
                id="faq-9"
              >
                For bigger projects like a full website or app design, I break
                them down into smaller, manageable milestones. This way, you'll
                see consistent progress with updates delivered every couple of
                days until your entire project is complete and looking sharp.
              </div>
            </div>
          </div>
        </div>

        <div>
          <h3 class="text-2xl font-bold text-center mb-8 text-primary animate-on-scroll">
            Basic Questions
          </h3>
          <div class="space-y-4" id="faq-accordion-basic">
            <div class="bg-gray-100 rounded-lg overflow-hidden animate-on-scroll">
              <button
                class="w-full px-6 py-4 text-left font-semibold flex justify-between items-center faq-toggle"
                data-faq="faq-2"
                aria-expanded="false"
              >
                <span>How does onboarding work?</span>
                <svg
                  class="w-5 h-5 transform transition-transform"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  ></path>
                </svg>
              </button>
              <div
                class="px-6 pb-4 hidden text-gray-600 text-descriptive"
                id="faq-2"
              >
                Getting started is a breeze. Once you subscribe, I'll set you up
                with your own private Trello board—usually within the hour. Just
                accept the invite, and you're ready to start submitting
                requests! I've left some simple instructions on the board to
                guide you.
              </div>
            </div>
            <div
              class="bg-gray-100 rounded-lg overflow-hidden animate-on-scroll delay-1"
            >
              <button
                class="w-full px-6 py-4 text-left font-semibold flex justify-between items-center faq-toggle"
                data-faq="faq-3"
                aria-expanded="false"
              >
                <span>Who are the developers and designers?</span>
                <svg
                  class="w-5 h-5 transform transition-transform"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  ></path>
                </svg>
              </button>
              <div
                class="px-6 pb-4 hidden text-gray-600 text-descriptive"
                id="faq-3"
              >
                You're looking at him! DesignSpark is a one-man creative and web
                development studio,founded and run by me, David. I handle every
                project personally from start to finish. This means no middlemen
                and no outsourcing— just a direct, dedicated partnership with
                you.
              </div>
            </div>
            <div
              class="bg-gray-100 rounded-lg overflow-hidden animate-on-scroll delay-2"
            >
              <button
                class="w-full px-6 py-4 text-left font-semibold flex justify-between items-center faq-toggle"
                data-faq="faq-7"
                aria-expanded="false"
              >
                <span>How do quick fixes work?</span>
                <svg
                  class="w-5 h-5 transform transition-transform"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  ></path>
                </svg>
              </button>
              <div
                class="px-6 pb-4 hidden text-gray-600 text-descriptive"
                id="faq-7"
              >
                The development process is straightforward. As long as your
                request is supported by your current platform, DesignSpark will
                handle the fix and development for you. Just submit your fix
                request, and I'll take care of the rest.
              </div>
            </div>
            <div
              class="bg-gray-100 rounded-lg overflow-hidden animate-on-scroll delay-3"
            >
              <button
                class="w-full px-6 py-4 text-left font-semibold flex justify-between items-center faq-toggle"
                data-faq="faq-10"
                aria-expanded="false"
              >
                <span>What programs do you design in?</span>
                <svg
                  class="w-5 h-5 transform transition-transform"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                  ></path>
                </svg>
              </button>
              <div
                class="px-6 pb-4 hidden text-gray-600 text-descriptive"
                id="faq-10"
              >
                I primarily use Figma for all design work. It's the industry
                standard for a reason—it's super collaborative, easy to use, and
                lets me deliver top-notch designs efficiently. If you have any
                other file format needs, just let me know, and I'll do my best
                to accommodate.
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="contact" class="py-20 px-4 bg-white">
      <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16 animate-on-scroll">
          <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
            Get In Touch <span class="text-primary">With Us</span>
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Have questions or ready to get started? We're here to help.
          </p>
        </div>

        <div
          class="max-w-lg mx-auto bg-white rounded-3xl shadow-2xl p-8 md:p-12 animate-on-scroll"
        >
          <form
            id="contactForm"
            action="process-contact-form.php"
            method="POST"
            class="space-y-6"
          >
            <div>
              <label
                for="name"
                class="block text-sm font-semibold text-gray-700 mb-2"
                >Your Name</label
              >
              <input
                type="text"
                id="name"
                name="name"
                required
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-gray-900 bg-white"
                placeholder="John Doe"
              />
            </div>

            <div>
              <label
                for="email"
                class="block text-sm font-semibold text-gray-700 mb-2"
                >Your Email</label
              >
              <input
                type="email"
                id="email"
                name="email"
                required
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-gray-900 bg-white"
                placeholder="you@example.com"
              />
            </div>

            <div>
              <label
                for="message"
                class="block text-sm font-semibold text-gray-700 mb-2"
                >Message</label
              >
              <textarea
                id="message"
                name="message"
                rows="4"
                required
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-gray-900 bg-white"
                placeholder="How can we help you?"
              ></textarea>
            </div>

            <div class="text-center">
              <button
                type="submit"
                class="w-full bg-gradient-to-r from-primary to-secondary text-white px-8 py-4 rounded-xl text-lg font-semibold inline-flex items-center justify-center space-x-3 hover:scale-105 transition-transform shadow-lg hover:shadow-xl active:scale-95 active:brightness-90"
              >
                <svg
                  class="w-6 h-6"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 10V3L4 14h7v7l9-11h-7z"
                  ></path>
                </svg>
                <span>Send Message</span>
              </button>
            </div>
          </form>
          
          <!-- Success Message -->
          <div id="formSuccessMessage" class="hidden mt-6 p-4 bg-green-50 border border-green-200 rounded-xl">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
              </svg>
              <p class="text-green-800 font-medium">Message sent successfully! We'll get back to you soon.</p>
            </div>
          </div>
          
          <!-- Error Message -->
          <div id="formErrorMessage" class="hidden mt-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
              </svg>
              <p class="text-red-800 font-medium">Error message will appear here</p>
            </div>
          </div>
          
          <div class="mt-8 text-center text-xs md:text-sm text-gray-500">
            <p>
              We typically respond within 1 business day. For urgent inquiries,
              call us at
              <a href="tel:+18012101118" class="text-primary font-semibold"
                >+1 (801) 210-1118</a
              >.
            </p>
          </div>
        </div>
      </div>
    </section>

    <footer class="py-10 px-4 bg-gray-800">
      <div class="max-w-6xl mx-auto text-center">
        <div class="text-white mb-6">
          &copy; 2025 DesignSpark. All rights reserved.
          <p>Based in Holladay Utah</p>
        </div>
        <div class="flex justify-center items-center space-x-4">
          <a
            href="#"
            onclick="openModal('privacyModal');return false;"
            class="text-gray-400 hover:text-white transition-colors"
          >
            Privacy Policy
          </a>
          <span class="text-gray-600">|</span>
          <a
            href="#"
            onclick="openModal('termsModal');return false;"
            class="text-gray-400 hover:text-white transition-colors"
          >
            Terms of Service
          </a>
        </div>
      </div>
    </footer>

    <div
      id="privacyModal"
      class="fixed inset-0 z-50 pointer-events-none hidden flex items-center justify-center"
    >
      <div
        onclick="closeModal('privacyModal')"
        class="absolute inset-0 bg-black bg-opacity-70"
      ></div>
      <div
        class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 p-8"
      >
        <button
          onclick="closeModal('privacyModal')"
          class="absolute top-2 right-2 text-gray-500 hover:text-gray-900"
        >
          <svg
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            ></path>
          </svg>
        </button>
        <h2 class="text-2xl font-bold mb-4">Privacy Policy</h2>
        <div
          class="text-gray-700 text-sm space-y-4 max-h-[70vh] overflow-y-auto"
        >
          <p>
            We respect your privacy and are committed to protecting your
            personal information. Any information you provide through our
            website (such as your name, email, or website URL) will only be used
            to provide our services and communicate with you. We do not sell,
            rent, or share your information with third parties except as
            required to deliver our services or comply with the law.
          </p>
          <p>
            We use industry-standard security measures to protect your data. By
            using our website, you consent to our collection and use of your
            information as described in this policy. If you have any questions
            about our privacy practices, please contact us.
          </p>
        </div>
      </div>
    </div>

    <div
      id="termsModal"
      class="fixed inset-0 z-50 pointer-events-none hidden flex items-center justify-center"
    >
      <div
        onclick="closeModal('termsModal')"
        class="absolute inset-0 bg-black bg-opacity-70"
      ></div>
      <div
        class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 p-8"
      >
        <button
          onclick="closeModal('termsModal')"
          class="absolute top-2 right-2 text-gray-500 hover:text-gray-900"
        >
          <svg
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            ></path>
          </svg>
        </button>
        <h2 class="text-2xl font-bold mb-4">Terms of Service</h2>
        <div
          class="text-gray-700 text-sm space-y-4 max-h-[70vh] overflow-y-auto"
        >
          <p>
            By using DesignSpark, you agree to the following terms and
            conditions. Our services are provided "as is" and without warranties
            of any kind, either express or implied. We strive to deliver
            high-quality work, but we do not guarantee that your website will be
            error-free, uninterrupted, or achieve specific results.
          </p>
          <p>
            <strong>Subscriptions:</strong> If you subscribe to a recurring plan
            (such as the Monthly Club), you authorize us to charge your payment
            method on a recurring basis until you pause or cancel your
            subscription. You may cancel or pause your subscription at any time,
            and your service will continue until the end of the current billing
            period. No refunds are provided for partial months.
          </p>
          <p>
            <strong>Limitation of Liability:</strong> In no event shall
            DesignSpark, its owners, or affiliates be liable for any direct,
            indirect, incidental, special, or consequential damages arising from
            the use or inability to use our services, including but not limited
            to loss of data, profits, or business interruption. You agree to
            indemnify and hold us harmless from any claims arising from your use
            of our services.
          </p>
          <p>
            <strong>Service Limitations:</strong> We reserve the right to refuse
            service, terminate accounts, or cancel orders at our sole
            discretion. We are not responsible for issues caused by third-party
            platforms, hosting providers, or changes made to your website by you
            or others after our work is completed.
          </p>
          <p>
            These terms may be updated at any time. Continued use of our
            services constitutes acceptance of any changes.
          </p>
        </div>
      </div>
    </div>

    <!-- Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>
    
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        // Smooth scroll function
        function scrollToSection(sectionId) {
          const section = document.getElementById(sectionId);
          if (section) {
            window.scrollTo({
              top: section.offsetTop - 80,
              behavior: "smooth",
            });
          }
        }
        window.scrollToEvaluation = () => scrollToSection("website-evaluation");
        window.scrollToPricing = () => scrollToSection("pricing");
        // Scroll functions
        function scrollToForm() {
          scrollToSection("contact");
        }

        function scrollToTestimonials() {
          scrollToSection("testimonials");
        }

        // Contact Form AJAX handler
        const contactForm = document.getElementById("contactForm");
        if (contactForm) {
          contactForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            const submitButtonText = submitButton.querySelector('span');
            const successMessage = document.getElementById("formSuccessMessage");
            const errorMessage = document.getElementById("formErrorMessage");
            
            // Hide any existing messages
            successMessage.classList.add("hidden");
            errorMessage.classList.add("hidden");
            
            // Show loading state
            submitButton.disabled = true;
            submitButtonText.textContent = "Sending...";
            submitButton.style.opacity = "0.7";
            
            fetch("process-contact-form.php", {
              method: "POST",
              body: formData,
            })
              .then((response) => response.json())
              .then((data) => {
                if (data.success) {
                  form.style.display = "none";
                  successMessage.classList.remove("hidden");
                } else {
                  const errorText = errorMessage.querySelector('p');
                  errorText.textContent = data.message || "An error occurred. Please try again.";
                  errorMessage.classList.remove("hidden");
                }
              })
              .catch((error) => {
                console.error("Network error:", error);
                const errorText = errorMessage.querySelector('p');
                errorText.textContent = "A network error occurred. Please check your connection and try again.";
                errorMessage.classList.remove("hidden");
              })
              .finally(() => {
                // Reset button state
                submitButton.disabled = false;
                submitButtonText.textContent = "Send Message";
                submitButton.style.opacity = "1";
              });
          });
        }

        // =================================================================
        // START: WEBSITE EVALUATION TOOL JAVASCRIPT
        // =================================================================
        const toolContainer = document.getElementById(
          "evaluation-tool-container"
        );


        
        toolContainer.innerHTML = `

                <div id="start-screen" class="text-center bg-white p-8 rounded-2xl shadow-lg fade-in">

                              <!-- Tool Header -->
            <div class="text-center mb-8 fade-in">
                <div
                    class="inline-flex items-center bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    100% Free - No Credit Card Required
                </div>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Free <span class="text-primary">Website Evaluation</span>
                </h2>
                <p class="text-sm md:text-md text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Get an AI powered comprehensive UX and conversion analysis of your website in
                    minutes. Discover what's holding back your conversions and get
                    actionable recommendations to improve your results.
                </p>
            </div>
                    <div class="max-w-md mx-auto space-y-4">
                        <div><input id="eval_name_input" type="text" placeholder="Your Name" required class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500"><p id="name-error" class="text-red-500 mt-2 text-sm text-left hidden">Please enter your name.</p></div>
                        <div><input id="eval_email_input" type="email" placeholder="you@email.com" required class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500"><p id="email-error" class="text-red-500 mt-2 text-sm text-left hidden">Please enter a valid email address.</p></div>
                        <div><input id="eval_website_url_input" type="url" placeholder="https://yourwebsite.com" required class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500"><p id="url-error" class="text-red-500 mt-2 text-sm text-left hidden">Please enter a valid URL.</p></div>
                        <div class="flex items-start space-x-3 text-left"><input id="newsletter_checkbox" name="newsletter" type="checkbox" value="1" checked class="mt-1 h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"><div class="text-sm"><label for="newsletter_checkbox" class="text-xs font-medium text-gray-700">Yes, you may contact me and send me occasional insights about turning more visitors into customers — no spam, promise.</label></div></div>
                        <button id="start-btn" class="w-full bg-gradient-to-r from-primary to-secondary text-white px-8 py-4 rounded-xl text-lg font-semibold inline-flex items-center justify-center space-x-3 hover:scale-105 transition-transform shadow-lg"><span>Start Free Analysis</span></button>
                    </div>
                    <div class="mt-8 text-center text-sm text-gray-500">
                    <div class="flex items-center justify-center space-x-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            No signup required
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Results in minutes
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            100% Free
                        </div>
                    </div>
                </div>
                </div>
                <div id="processing-screen" class="hidden bg-white p-8 rounded-2xl shadow-lg text-center">
                    <h2 class="text-2xl font-bold mb-2">AI Is Analyzing Your Website...</h2>
                    <p class="text-slate-600 mb-8">This can take up to a minute. We're saving your info and sending the site to the AI.</p>
                    <div id="tracker-container" class="space-y-3 max-w-md mx-auto text-left"></div>
                </div>
                <div id="results-screen" class="hidden text-center">
                    <div class="bg-white p-8 rounded-2xl shadow-lg">
                        <div id="results-url" class="mb-4 text-sm text-indigo-700 font-semibold break-all"></div>
                        <h2 class="text-3xl font-bold mb-2">Your Analysis is Complete!</h2>
                        <p class="text-slate-600 mb-8">Here's your website's initial score. For detailed recommendations, order the full report.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                            <div class="relative flex justify-center items-center h-[18rem] w-[18rem] mx-auto">
                                <svg class="w-[18rem] h-[18rem] transform -rotate-90"><circle class="text-slate-200" stroke-width="16" stroke="currentColor" fill="transparent" r="80" cx="144" cy="144" /><circle id="score-circle" class="text-indigo-600 score-circle" stroke-width="16" stroke-linecap="round" stroke="currentColor" fill="transparent" r="80" cx="144" cy="144" /></svg>
                                <div class="absolute flex flex-col items-center"><span id="overall-score-text" class="text-4xl font-extrabold text-slate-900">0</span><span class="text-slate-500 -mt-1 text-xl">Overall Score</span></div>
                            </div>
                            <div id="category-scores" class="space-y-4 text-left"></div>
                        </div>
                        <div id="feedback-section" class="mt-12 text-left border-t border-slate-200 pt-8">
                            <h3 class="text-2xl font-bold mb-4">Top Opportunities</h3>
                            <div id="feedback-list" class="space-y-6"></div>
                        </div>
                        <div class="mt-12 border-t border-slate-200 pt-8 text-center">
                            <h3 class="text-2xl font-bold text-slate-800 mb-2">Ready to Turn Insights into Action?</h3>
                            <p class="text-slate-600 max-w-xl mx-auto mb-6">This free report shows your biggest opportunities. The full, detailed report provides a step-by-step guide to fixing these issues and increasing conversions.</p>
                            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                                <button id="download-basic-report-btn" class="w-full sm:w-auto bg-blue-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    Download Basic Report (PDF)
                                </button>
                                <button id="restart-btn" class="w-full sm:w-auto bg-slate-200 text-slate-800 font-bold py-3 px-8 rounded-lg hover:bg-slate-300 transition">Analyze Another Site</button>
                                <button id="order-advanced-btn" class="w-full sm:w-auto bg-green-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-green-700 transition-transform transform hover:scale-105">Get the Full Report for $19.99</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="advanced-report-screen" class="hidden">
                    <div id="purchase-modal" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center p-4 fade-in z-50">
                        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-8 text-center slide-in-up">
                            <h2 class="text-2xl font-bold mb-2">Unlock Your Detailed Report</h2>
                            <p class="text-slate-600 mb-6">Get the full, detailed report with all the steps to improve your site.</p>
                            <div class="bg-slate-100 p-6 rounded-lg mb-6"><div class="flex justify-between items-center text-lg"><span class="font-medium">Detailed Website Report</span><span class="font-bold text-indigo-600">$19.99</span></div></div>
                            <button id="stripe-purchase-btn" class="w-full bg-indigo-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-indigo-700 transition">Secure Payment</button>
                            <button id="cancel-purchase-btn" class="mt-3 text-sm text-slate-500 hover:text-slate-700">Cancel and go back</button>
                        </div>
                    </div>
                    <div id="advanced-report-content" class="hidden bg-white p-8 rounded-2xl shadow-lg">
                        <div id="advanced-url" class="mb-4 text-sm text-indigo-700 font-semibold break-all"></div>
                        <div class="no-print flex justify-between items-center mb-8 pb-4">
                             <h2 class="text-3xl font-bold">Detailed Report</h2>
                            <div class="flex gap-2">
                                <button onclick="window.print()" class="bg-slate-200 text-slate-800 font-bold py-2 px-4 rounded-lg hover:bg-slate-300 transition flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                    Print
                                </button>
                                <button onclick="window.downloadReport()" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    Download PDF
                                </button>
                            </div>
                        </div>

                        <div id="advanced-feedback-list" class="space-y-8"></div>
                        <div id="ab-test-accordion" class="hidden no-print mt-12">
                            <button id="ab-test-toggle" class="w-full text-left font-bold text-xl p-4 bg-slate-100 rounded-lg flex justify-between items-center hover:bg-slate-200 transition">
                                <span>Bonus: A/B Test Ideas for Experts</span>
                                <svg id="ab-test-arrow" class="w-6 h-6 transition-transform" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            <div id="ab-test-content" class="hidden p-4 border border-t-0 border-slate-200 rounded-b-lg">
                                <div id="ab-test-list" class="space-y-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
              `;

        // --- Tool's DOM ELEMENTS ---
        const startScreen = document.getElementById("start-screen");
        const processingScreen = document.getElementById("processing-screen");
        const resultsScreen = document.getElementById("results-screen");
        const advancedReportScreen = document.getElementById(
          "advanced-report-screen"
        );
        const purchaseModal = document.getElementById("purchase-modal");
        const advancedReportContent = document.getElementById(
          "advanced-report-content"
        );
        const startBtn = document.getElementById("start-btn");
        const restartBtn = document.getElementById("restart-btn");
        const orderAdvancedBtn = document.getElementById("order-advanced-btn");
        const cancelPurchaseBtn = document.getElementById(
          "cancel-purchase-btn"
        );
        const nameInput = document.getElementById("eval_name_input");
        const emailInput = document.getElementById("eval_email_input");
        const websiteUrlInput = document.getElementById(
          "eval_website_url_input"
        );
        const newsletterCheckbox = document.getElementById(
          "newsletter_checkbox"
        );
        const nameError = document.getElementById("name-error");
        const emailError = document.getElementById("email-error");
        const urlError = document.getElementById("url-error");
        const trackerContainer = document.getElementById("tracker-container");
        const overallScoreText = document.getElementById("overall-score-text");
        const scoreCircle = document.getElementById("score-circle");
        const categoryScoresContainer =
          document.getElementById("category-scores");
        const feedbackList = document.getElementById("feedback-list");
        const resultsUrl = document.getElementById("results-url");
        const advancedUrl = document.getElementById("advanced-url");
        const advancedFeedbackList = document.getElementById(
          "advanced-feedback-list"
        );
        const abTestAccordion = document.getElementById("ab-test-accordion");
        const abTestToggle = document.getElementById("ab-test-toggle");
        const abTestContent = document.getElementById("ab-test-content");
        const abTestArrow = document.getElementById("ab-test-arrow");
        const abTestList = document.getElementById("ab-test-list");

        let aiReportData = null;
        let currentReportId = null;

        function isValidUrl(string) {
          try {
            new URL(string);
            return true;
          } catch (_) {
            return false;
          }
        }
        function isValidEmail(email) {
          return /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
            String(email).toLowerCase()
          );
        }

        function startEvaluation() {
          let isValid = true;
          let urlToTest = websiteUrlInput.value.trim();
          if (!/^https?:\/\//i.test(urlToTest) && urlToTest.length > 0) {
            urlToTest = "https://" + urlToTest;
            websiteUrlInput.value = urlToTest;
          }
          if (!nameInput.value.trim()) {
            nameError.classList.remove("hidden");
            isValid = false;
          } else {
            nameError.classList.add("hidden");
          }
          if (!isValidEmail(emailInput.value.trim())) {
            emailError.classList.remove("hidden");
            isValid = false;
          } else {
            emailError.classList.add("hidden");
          }
          if (!isValidUrl(urlToTest)) {
            urlError.classList.remove("hidden");
            isValid = false;
          } else {
            urlError.classList.add("hidden");
          }
          if (!isValid) return;

          startScreen.classList.add("fade-out");
          setTimeout(() => {
            startScreen.classList.add("hidden");
            processingScreen.classList.remove("hidden");
            processingScreen.classList.add("fade-in");
            runAutomatedChecks(
              nameInput.value.trim(),
              emailInput.value.trim(),
              urlToTest,
              newsletterCheckbox.checked
            );
          }, 500);
        }

        function runTrackerAnimation(onComplete) {
          const checks = [
            { id: "db-save", name: "Saving your request..." },
            { id: "ai-call", name: "Sending to our AI..." },
            { id: "parsing", name: "Translating robot language..." },
            { id: "report", name: "Making it look pretty..." },
            { id: "waiting", name: "AI is mulling things over..." },
          ];
          
          const waitingMessages = [
            "AI is mulling things over...",
            "Processing your website data...",
            "Analyzing user experience...",
            "Checking performance metrics...",
            "Evaluating design patterns...",
            "Almost ready...",
          ];
          
          trackerContainer.innerHTML = "";
          checks.forEach((check) => {
            trackerContainer.innerHTML += `<div id="tracker-${check.id}" class="tracker-item flex items-center p-4 bg-slate-100 rounded-lg"><div class="tracker-icon w-6 h-6 mr-4 flex-shrink-0 flex items-center justify-center"><svg class="pending-icon text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><svg class="spinner text-indigo-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg><svg class="checkmark text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg></div><span class="tracker-text font-medium text-slate-700">${check.name}</span></div>`;
          });
          
          let currentCheckIndex = 0;
          let waitingMessageIndex = 0;
          let waitingInterval = null;
          
          const interval = setInterval(() => {
            if (currentCheckIndex > 0)
              document
                .getElementById(`tracker-${checks[currentCheckIndex - 1].id}`)
                .classList.add("complete");
            if (currentCheckIndex < checks.length - 1) {
              document
                .getElementById(`tracker-${checks[currentCheckIndex].id}`)
                .classList.add("in-progress");
              currentCheckIndex++;
            } else if (currentCheckIndex === checks.length - 1) {
              // Start the waiting step with cycling messages
              document
                .getElementById(`tracker-${checks[currentCheckIndex].id}`)
                .classList.add("in-progress");
              currentCheckIndex++;
              
              // Start cycling through waiting messages
              waitingInterval = setInterval(() => {
                const waitingElement = document.querySelector('#tracker-waiting .tracker-text');
                if (waitingElement) {
                  waitingElement.textContent = waitingMessages[waitingMessageIndex];
                  waitingMessageIndex = (waitingMessageIndex + 1) % waitingMessages.length;
                }
              }, 3600);
            } else {
              clearInterval(interval);
              if (waitingInterval) clearInterval(waitingInterval);
              onComplete();
            }
          }, 3600);
          
          // Store intervals for cleanup
          window.trackerInterval = interval;
          window.waitingInterval = waitingInterval;
        }

        function runAutomatedChecks(name, email, url, newsletter) {
          let trackerDone = false,
            apiDone = false,
            apiError = null;
          const formData = new FormData();
          formData.append("name", name);
          formData.append("email", email);
          formData.append("website_url", url);
          formData.append("newsletter", newsletter ? "1" : "0");
          fetch("api_handler.php", { method: "POST", body: formData })
            .then((response) => response.json())
            .then((data) => {
              if (data.error) throw new Error(data.error);
              aiReportData = data;
              currentReportId = data.reportId; // Store the report ID for later use
            })
            .catch((error) => {
              apiError = error.message;
            })
            .finally(() => {
              apiDone = true;
              if (trackerDone) showResults(apiError);
            });
          runTrackerAnimation(() => {
            trackerDone = true;
            if (apiDone) showResults(apiError);
          });
        }

        function showResults(error = null) {
          // Clean up any running intervals
          if (window.trackerInterval) {
            clearInterval(window.trackerInterval);
            window.trackerInterval = null;
          }
          if (window.waitingInterval) {
            clearInterval(window.waitingInterval);
            window.waitingInterval = null;
          }
          
          // Complete the waiting step
          const waitingElement = document.getElementById('tracker-waiting');
          if (waitingElement) {
            waitingElement.classList.add("complete");
            waitingElement.querySelector('.tracker-text').textContent = "Analysis complete!";
          }
          
          if (error || !aiReportData) {
            alert(
              `An error occurred: ${error || "Could not retrieve AI report."}`
            );
            restart();
            return;
          }
          processingScreen.classList.add("fade-out");
          setTimeout(() => {
            processingScreen.classList.add("hidden");
            resultsScreen.classList.remove("hidden");
            resultsScreen.classList.add("fade-in");
            resultsUrl.textContent = "Analyzed Site: " + websiteUrlInput.value;
            displayInitialReport();
          }, 500);
        }

        function displayInitialReport() {
          const { overallScore, basicReport } = aiReportData;
          const { scores, topOpportunities } = basicReport;
          const metricDescriptions = {
            HeroClarity: "How clear is your main message?",
            VisualDesignLayout: "How professional does it look?",
            CallToAction: "Is it obvious what to do next?",
            ReadabilityTypography: "Is the text easy to read?",
            SocialProofTrust: "Does your site build trust?",
          };
          const scoresArray = Object.keys(scores).map((key) => ({
            metric: key,
            score: scores[key],
          }));
          categoryScoresContainer.innerHTML = "";
          scoresArray.forEach((cat, index) => {
            const colors = getScoreColor(cat.score);
            categoryScoresContainer.innerHTML += `<div class="slide-in-up" style="animation-delay: ${
              index * 100
            }ms"><div class="flex justify-between items-center mb-1"><span class="font-semibold">${formatMetricName(
              cat.metric
            )}</span><span class="text-sm font-bold ${colors.bg} ${
              colors.text
            } px-2.5 py-1 rounded-md">${
              cat.score
            }</span></div><p class="text-sm text-slate-500 text-left mb-2">${
              metricDescriptions[cat.metric] || ""
            }</p><div class="w-full bg-slate-200 rounded-full h-2.5"><div class="h-2.5 rounded-full ${
              colors.bar
            }" style="width: ${cat.score}%"></div></div></div>`;
          });
          feedbackList.innerHTML = "";
          topOpportunities.forEach((opp) => {
            feedbackList.innerHTML += `<div class="flex gap-4"><div class="w-12 h-12 rounded-full bg-indigo-100 flex-shrink-0 flex items-center justify-center"><svg class="w-6 h-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg></div><div><p class="text-slate-600">${opp}</p></div></div>`;
          });
          // Fix the score circle animation
          const circle = scoreCircle;
          const circumference = circle.r.baseVal.value * 2 * Math.PI;
          
          // Set initial state - completely empty circle
          circle.style.strokeDasharray = circumference;
          circle.style.strokeDashoffset = circumference;
          
          // Animate the score counter
          let currentScore = 0;
          const scoreInterval = setInterval(() => {
            if (currentScore >= overallScore) {
              clearInterval(scoreInterval);
              overallScoreText.textContent = overallScore;
            } else {
              currentScore++;
              overallScoreText.textContent = currentScore;
            }
          }, 20);

          // Animate the circle fill after a short delay
          setTimeout(() => {
            const offset = circumference - (overallScore / 100) * circumference;
            circle.style.strokeDashoffset = offset;
          }, 200);
        }

        function showAdvancedReportFlow() {
          resultsScreen.classList.add("fade-out");
          setTimeout(() => {
            resultsScreen.classList.add("hidden");
            advancedReportScreen.classList.remove("hidden");
            purchaseModal.classList.remove("hidden");
            advancedUrl.textContent = "Analyzed Site: " + websiteUrlInput.value;
          }, 500);
        }

        function generateAdvancedFeedback() {
          if (!aiReportData || !aiReportData.advancedReport) {
            advancedFeedbackList.innerHTML = `<p class="text-red-500">Could not display the detailed report. Please contact support.</p>`;
            return;
          }
          const { scores, advancedFeedback, abTestIdeas } =
            aiReportData.advancedReport;
          const metricIcons = {
            HeroClarity: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>`,
            VisualDesignLayout: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>`,
            CallToAction: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 15h14"/><path d="m12 5 7 10H5Z"/></svg>`,
            ReadabilityTypography: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/></svg>`,
            SocialProofTrust: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`,
            HeroTrustSignals: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>`,
            PersuasiveCopy: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>`,
            AttentionRatio: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20v-6M6 20v-4M18 20v-2"/></svg>`,
            NavigationClarity: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>`,
            AccessibilityContrast: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 18a6 6 0 0 0 0-12v12z"/></svg>`,
            MobileResponsiveHints: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></svg>`,
          };
          const feedbackArray = Object.keys(advancedFeedback).map((key) => ({
            category: key,
            ...advancedFeedback[key],
          }));
          advancedFeedbackList.innerHTML = "";
          feedbackArray.forEach((catFeedback) => {
            const scoreValue = scores[catFeedback.category] || -1;
            const colors = getScoreColor(scoreValue);
            const icon =
              metricIcons[catFeedback.category] ||
              metricIcons.VisualDesignLayout;
            advancedFeedbackList.innerHTML += `<div class="p-6 border border-slate-200 rounded-xl"><div class="flex gap-6 items-start"><div class="flex-shrink-0 flex flex-col items-center"><div class="w-16 h-16 rounded-full ${
              colors.bg
            } ${
              colors.text
            } flex-shrink-0 flex items-center justify-center font-bold text-2xl">${
              scoreValue === -1 ? "N/A" : scoreValue
            }</div><div class="w-12 h-12 mt-3 flex items-center justify-center text-slate-500">${icon}</div></div><div class="flex-grow"><h3 class="text-2xl font-bold">${formatMetricName(
              catFeedback.category
            )}</h3><div class="mt-2 text-slate-700 leading-relaxed space-y-3"><p>${
              catFeedback.feedback
            }</p><p class="p-3 bg-slate-50 border-l-4 border-slate-200 italic"><span class="font-semibold not-italic">Example:</span> ${
              catFeedback.example
            }</p></div></div></div></div>`;
          });
          if (Array.isArray(abTestIdeas) && abTestIdeas.length > 0) {
            abTestAccordion.classList.remove("hidden");
            abTestList.innerHTML = "";
            abTestIdeas.forEach((idea) => {
              abTestList.innerHTML += `<div class="bg-slate-100 p-3 rounded-lg flex items-start gap-3"><span class="text-indigo-500 font-bold">Test:</span><p class="text-slate-700">${idea}</p></div>`;
            });
          }
        }

        function getScoreColor(percentage) {
          if (percentage === -1)
            return { bg: "bg-slate-100", text: "text-slate-500" };
          if (percentage >= 85)
            return {
              bg: "bg-green-100",
              text: "text-green-800",
              bar: "bg-green-500",
            };
          if (percentage >= 50)
            return {
              bg: "bg-yellow-100",
              text: "text-yellow-800",
              bar: "bg-yellow-500",
            };
          return { bg: "bg-red-100", text: "text-red-800", bar: "bg-red-500" };
        }
        function formatMetricName(name) {
          return name.replace(/([A-Z])/g, " $1").trim();
        }
        function restart() {
          const currentScreen = document.querySelector(
            "#evaluation-tool-container > div:not(.hidden)"
          );
          if (currentScreen) {
            currentScreen.classList.add("fade-out");
            setTimeout(() => {
              currentScreen.classList.add("hidden");
              startScreen.classList.remove("hidden", "fade-out");
              startScreen.classList.add("fade-in");
              websiteUrlInput.value = "";
              emailInput.value = "";
              nameInput.value = "";
            }, 500);
          }
        }

        startBtn.addEventListener("click", startEvaluation);
        restartBtn.addEventListener("click", restart);
        orderAdvancedBtn.addEventListener("click", showAdvancedReportFlow);
        document.getElementById("download-basic-report-btn").addEventListener("click", downloadBasicReport);
        cancelPurchaseBtn.addEventListener("click", () => {
          advancedReportScreen.classList.add("hidden");
          resultsScreen.classList.remove("hidden", "fade-out");
          resultsScreen.classList.add("fade-in");
        });

        // Initialize Stripe with the configured publishable key
        const stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');
        
        document
          .getElementById("stripe-purchase-btn")
          .addEventListener("click", async () => {
            const button = document.getElementById("stripe-purchase-btn");
            const originalText = button.textContent;
            
            // Show loading state
            button.disabled = true;
            button.textContent = "Processing...";
            button.style.opacity = "0.7";
            
            try {
              // Create checkout session
              const response = await fetch('create-checkout-session.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                  customer_email: emailInput.value,
                  website_url: websiteUrlInput.value,
                  customer_name: nameInput.value,
                  report_id: currentReportId
                })
              });
              
              const session = await response.json();
              
              if (session.error) {
                throw new Error(session.error);
              }
              
              // Redirect to Stripe Checkout
              const result = await stripe.redirectToCheckout({
                sessionId: session.id
              });
              
              if (result.error) {
                throw new Error(result.error.message);
              }
              
            } catch (error) {
              console.error('Error:', error);
              alert('Payment failed: ' + error.message);
              
              // Reset button state
              button.disabled = false;
              button.textContent = originalText;
              button.style.opacity = "1";
            }
          });

        abTestToggle.addEventListener("click", () => {
          abTestContent.classList.toggle("hidden");
          abTestArrow.classList.toggle("rotate-180");
        });

        // Modal logic for policies
        function openModal(modalId) {
          const modal = document.getElementById(modalId);
          if (!modal) return;
          modal.classList.remove("pointer-events-none", "hidden");
          document.body.style.overflow = "hidden";
        }
        window.openModal = openModal;

        function closeModal(modalId) {
          const modal = document.getElementById(modalId);
          if (!modal) return;
          modal.classList.add("pointer-events-none", "hidden");
          document.body.style.overflow = "";
        }
        window.closeModal = closeModal;

        // Portfolio Modal Functions
        window.openPortfolioModal = function (imgSrc) {
          const modal = document.getElementById("portfolioModal");
          document.getElementById("modalImage").src = imgSrc;
          modal.classList.remove("pointer-events-none", "hidden");
          setTimeout(() => {
            document.getElementById("modalBg").classList.add("opacity-100");
            document
              .getElementById("modalContent")
              .classList.add("opacity-100", "scale-100");
          }, 10);
          document.body.style.overflow = "hidden";
        };
        window.closePortfolioModal = function () {
          const modal = document.getElementById("portfolioModal");
          document.getElementById("modalBg").classList.remove("opacity-100");
          document
            .getElementById("modalContent")
            .classList.remove("opacity-100", "scale-100");
          setTimeout(() => {
            modal.classList.add("pointer-events-none", "hidden");
            document.body.style.overflow = "";
          }, 300);
        };

        // PDF download function for basic report
        function downloadBasicReport() {
          console.log("downloadBasicReport called", aiReportData);
          
          const { jsPDF } = window.jspdf;
          const doc = new jsPDF();
          const analyzedUrl = document.getElementById("results-url").textContent;

          if (!aiReportData) {
            console.log("No aiReportData available");
            doc.text("No report data available.", 10, 10);
            doc.save("Basic-Website-Report.pdf");
            return;
          }

          const { overallScore, basicReport } = aiReportData;
          const { scores, topOpportunities } = basicReport;

          console.log("Basic report data:", { overallScore, scores, topOpportunities });

          let yPos = 20;
          const leftMargin = 15;
          const rightMargin = 195;
          const lineSpacing = 7;

          // Add analyzed URL at the top
          if (analyzedUrl) {
            doc.setFontSize(11);
            doc.setTextColor(54, 78, 163);
            doc.text(analyzedUrl, leftMargin, yPos);
            yPos += lineSpacing * 1.5;
            doc.setTextColor(0, 0, 0);
          }

          doc.setFontSize(22);
          doc.setFont("helvetica", "bold");
          doc.text("Free Website Evaluation Report", leftMargin, yPos);
          yPos += lineSpacing * 2;

          doc.setFontSize(16);
          doc.text(`Overall Score: ${overallScore}`, leftMargin, yPos);
          yPos += lineSpacing * 2;

          // Add category scores
          doc.setFontSize(14);
          doc.setFont("helvetica", "bold");
          doc.text("Category Scores", leftMargin, yPos);
          yPos += lineSpacing;

          doc.setFontSize(11);
          doc.setFont("helvetica", "normal");

          Object.keys(scores).forEach((category) => {
            const scoreValue = scores[category];
            const scoreText = scoreValue === -1 ? "N/A" : scoreValue;
            const formattedName = category.replace(/([A-Z])/g, " $1").trim();
            
            doc.text(`${formattedName}: ${scoreText}`, leftMargin, yPos);
            yPos += lineSpacing;
          });

          yPos += lineSpacing;

          // Add top opportunities
          doc.setFontSize(14);
          doc.setFont("helvetica", "bold");
          doc.text("Top Opportunities", leftMargin, yPos);
          yPos += lineSpacing * 1.5;

          doc.setFontSize(11);
          doc.setFont("helvetica", "normal");

          // topOpportunities is an array of strings, not an object
          if (Array.isArray(topOpportunities)) {
            topOpportunities.forEach((opportunity, index) => {
              if (yPos > 260) {
                // Check if new page is needed
                doc.addPage();
                yPos = 20;
              }

              doc.setFontSize(12);
              doc.setFont("helvetica", "bold");
              doc.text(`${index + 1}. Opportunity`, leftMargin, yPos);
              yPos += lineSpacing;

              doc.setFontSize(11);
              doc.setFont("helvetica", "normal");

              const opportunityLines = doc.splitTextToSize(
                opportunity,
                rightMargin - leftMargin
              );
              doc.text(opportunityLines, leftMargin, yPos);
              yPos += opportunityLines.length * lineSpacing + lineSpacing;
            });
          }

          // Add upgrade notice
          if (yPos > 240) {
            doc.addPage();
            yPos = 20;
          }
          
          yPos = Math.max(yPos, 220); // Push to bottom section
          
          doc.setFontSize(12);
          doc.setFont("helvetica", "bold");
          doc.setTextColor(34, 139, 34);
          doc.text("Want More Detailed Insights?", leftMargin, yPos);
          yPos += lineSpacing;
          
          doc.setFontSize(10);
          doc.setFont("helvetica", "normal");
          doc.setTextColor(0, 0, 0);
          doc.text("Get the full detailed report with step-by-step recommendations,", leftMargin, yPos);
          yPos += lineSpacing;
          doc.text("implementation examples, and A/B testing ideas for just $19.99.", leftMargin, yPos);
          yPos += lineSpacing * 2;

          // Add website link at the bottom
          doc.setFontSize(12);
          doc.setFont("helvetica", "bold");
          doc.setTextColor(54, 78, 163);
          doc.textWithLink(
            "Visit withsparkdesign.com for more website help",
            leftMargin,
            yPos + 15,
            { url: "https://www.withsparkdesign.com" }
          );
          doc.setTextColor(0, 0, 0);

          doc.save("Basic-Website-Report.pdf");
        }

        // PDF download function
        function downloadReport() {
          const { jsPDF } = window.jspdf;
          const doc = new jsPDF();
          const analyzedUrl = document.getElementById("advanced-url").textContent;

          if (!aiReportData) {
            doc.text("No report data available.", 10, 10);
            doc.save("AI-Website-Report.pdf");
            return;
          }

          const { overallScore, advancedReport } = aiReportData;
          const { scores, advancedFeedback, abTestIdeas } = advancedReport;

          let yPos = 20;
          const leftMargin = 15;
          const rightMargin = 195;
          const lineSpacing = 7;

          // Add analyzed URL at the top
          if (analyzedUrl) {
            doc.setFontSize(11);
            doc.setTextColor(54, 78, 163);
            doc.text(analyzedUrl, leftMargin, yPos);
            yPos += lineSpacing * 1.5;
            doc.setTextColor(0, 0, 0);
          }

          doc.setFontSize(22);
          doc.setFont("helvetica", "bold");
          doc.text("AI-Powered Website Evaluation", leftMargin, yPos);
          yPos += lineSpacing * 2;

          doc.setFontSize(16);
          doc.text(`Overall Score: ${overallScore}`, leftMargin, yPos);
          yPos += lineSpacing * 2;

          const feedbackArray = Object.keys(advancedFeedback).map((key) => ({
            category: key,
            ...advancedFeedback[key],
          }));

          feedbackArray.forEach((catFeedback) => {
            if (yPos > 260) {
              // Check if new page is needed
              doc.addPage();
              yPos = 20;
            }

            const scoreValue = scores[catFeedback.category];
            const scoreText = scoreValue === -1 ? "N/A" : scoreValue;
            const formattedName = (name) => name.replace(/([A-Z])/g, " $1").trim();

            doc.setFontSize(14);
            doc.setFont("helvetica", "bold");
            doc.text(
              `${formattedName(catFeedback.category)} (Score: ${scoreText})`,
              leftMargin,
              yPos
            );
            yPos += lineSpacing;

            doc.setFontSize(11);
            doc.setFont("helvetica", "normal");

            const feedbackLines = doc.splitTextToSize(
              catFeedback.feedback,
              rightMargin - leftMargin
            );
            doc.text(feedbackLines, leftMargin, yPos);
            yPos += feedbackLines.length * lineSpacing;

            const exampleLines = doc.splitTextToSize(
              `Example: ${catFeedback.example}`,
              rightMargin - leftMargin - 5
            );
            doc.setFont("helvetica", "italic");
            doc.text(exampleLines, leftMargin + 5, yPos);
            yPos += exampleLines.length * lineSpacing + lineSpacing * 1.5;
          });

          if (Array.isArray(abTestIdeas) && abTestIdeas.length > 0) {
            if (yPos > 250) {
              doc.addPage();
              yPos = 20;
            }
            doc.setFontSize(14);
            doc.setFont("helvetica", "bold");
            doc.text("A/B Test Ideas", leftMargin, yPos);
            yPos += lineSpacing;

            doc.setFontSize(11);
            doc.setFont("helvetica", "normal");
            abTestIdeas.forEach((idea) => {
              const ideaLines = doc.splitTextToSize(
                `- ${idea}`,
                rightMargin - leftMargin
              );
              doc.text(ideaLines, leftMargin, yPos);
              yPos += ideaLines.length * lineSpacing;
            });
          }

          // Add blurb at the bottom of the last page
          if (yPos > 240) {
            doc.addPage();
            yPos = 20;
          }
          yPos = Math.max(yPos, 250); // Push to bottom if not already
          doc.setFontSize(12);
          doc.setFont("helvetica", "bold");
          doc.setTextColor(54, 78, 163);
          doc.textWithLink(
            "Need more website help? Visit withsparkdesign.com",
            leftMargin,
            yPos + 15,
            { url: "https://www.withsparkdesign.com" }
          );
          doc.setTextColor(0, 0, 0);

          doc.save("AI-Website-Report.pdf");
        }

        // Make downloadReport and downloadBasicReport available globally
        window.downloadReport = downloadReport;
        window.downloadBasicReport = downloadBasicReport;
      });

      // basic functions

      // Modal Functions
      function openPortfolioModal(imgSrc) {
        const modal = document.getElementById("portfolioModal");
        const modalBg = document.getElementById("modalBg");
        const modalContent = document.getElementById("modalContent");
        const modalImg = document.getElementById("modalImage");
        modalImg.src = imgSrc;
        modal.classList.remove("pointer-events-none", "hidden");
        setTimeout(() => {
          modalBg.classList.add("opacity-100");
          modalBg.classList.remove("opacity-0");
          modalContent.classList.add("opacity-100", "scale-100");
          modalContent.classList.remove("opacity-0", "scale-95");
        }, 10);
        document.body.style.overflow = "hidden";
      }

      function closePortfolioModal() {
        const modal = document.getElementById("portfolioModal");
        const modalBg = document.getElementById("modalBg");
        const modalContent = document.getElementById("modalContent");
        modalBg.classList.remove("opacity-100");
        modalBg.classList.add("opacity-0");
        modalContent.classList.remove("opacity-100", "scale-100");
        modalContent.classList.add("opacity-0", "scale-95");
        setTimeout(() => {
          modal.classList.add("pointer-events-none", "hidden");
          document.body.style.overflow = "";
        }, 300);
      }

      function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.classList.remove("pointer-events-none", "hidden");
        document.body.style.overflow = "hidden";
      }

      function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.classList.add("pointer-events-none", "hidden");
        document.body.style.overflow = "";
      }

      document.addEventListener("DOMContentLoaded", function () {
        // Close modal on background click
        const modals = ["portfolioModal", "privacyModal", "termsModal"];
        modals.forEach((id) => {
          const modal = document.getElementById(id);
          if (modal) {
            modal.addEventListener("click", function (e) {
              if (
                e.target === modal ||
                e.target.id === "modalBg" ||
                e.target.classList.contains("bg-opacity-70")
              ) {
                id === "portfolioModal"
                  ? closePortfolioModal()
                  : closeModal(id);
              }
            });
          }
        });

        // FAQ Accordion
        document.querySelectorAll(".faq-toggle").forEach((button) => {
          button.addEventListener("click", () => {
            const content = document.getElementById(button.dataset.faq);
            const arrow = button.querySelector("svg");
            const isExpanded = button.getAttribute("aria-expanded") === "true";

            button.setAttribute("aria-expanded", !isExpanded);
            content.classList.toggle("hidden");
            arrow.classList.toggle("rotate-180");
          });
        });

        // Scroll Animation Observer
        const animationElements =
          document.querySelectorAll(".animate-on-scroll");
        const observer = new IntersectionObserver(
          (entries) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting) {
                entry.target.classList.add("is-visible");
              }
            });
          },
          {
            threshold: 0.1,
          }
        );

        animationElements.forEach((el) => {
          observer.observe(el);
        });
      });
    </script>
  </body>
</html>