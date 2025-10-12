// ===== تهيئة التطبيق عند تحميل الصفحة =====
document.addEventListener("DOMContentLoaded", function () {
  console.log("🚀 بدء تهيئة تطبيق دليل الفعاليات...");

  // تهيئة جميع الأنظمة
  initializeDarkMode();
  initializeScrollToTop();
  initializeEventSystem();
  initializeContactForm();
  initializeSearchFilter();
  initializeAnimations();

  console.log("✅ تم تهيئة التطبيق بنجاح");
});

// ===== نظام الوضع الليلي (Dark Mode) =====
function initializeDarkMode() {
  console.log("🌙 تهيئة نظام الوضع الليلي...");

  const themeToggle = document.getElementById("themeToggle");
  const themeIcon = document.getElementById("themeIcon");

  // التحقق من وجود العناصر
  if (!themeToggle || !themeIcon) {
    console.warn("⚠️ عناصر الوضع الليلي غير موجودة");
    return;
  }

  // الحصول على الوضع الحالي من localStorage أو استخدام الوضع النهاري افتراضيًا
  let currentTheme = localStorage.getItem("theme");
  if (!currentTheme) {
    // إذا لم يكن هناك تفضيل محفوظ، التحقق من تفضيلات النظام
    currentTheme = window.matchMedia("(prefers-color-scheme: dark)").matches
      ? "dark"
      : "light";
    localStorage.setItem("theme", currentTheme);
  }

  // تطبيق الوضع الحالي
  applyTheme(currentTheme);

  // إضافة مستمع حدث للنقر على الزر
  themeToggle.addEventListener("click", function (event) {
    event.preventDefault();
    event.stopPropagation();

    console.log("🖱️ تم النقر على زر الوضع الليلي");

    // تبديل الوضع
    currentTheme = currentTheme === "light" ? "dark" : "light";
    console.log("🔄 الانتقال إلى الوضع:", currentTheme);

    // تطبيق التغييرات
    applyTheme(currentTheme);
    localStorage.setItem("theme", currentTheme);

    // إشعار بصري
    showThemeNotification(currentTheme);
  });

  // استماع لتغير تفضيلات النظام
  window
    .matchMedia("(prefers-color-scheme: dark)")
    .addEventListener("change", function (e) {
      if (!localStorage.getItem("theme")) {
        const newTheme = e.matches ? "dark" : "light";
        applyTheme(newTheme);
      }
    });

  console.log("✅ نظام الوضع الليلي جاهز - الوضع الحالي:", currentTheme);
}

function applyTheme(theme) {
  // تطبيق السمة على عنصر HTML
  document.documentElement.setAttribute("data-theme", theme);

  // تحديث أيقونة الزر
  const themeIcon = document.getElementById("themeIcon");
  if (themeIcon) {
    themeIcon.textContent = theme === "light" ? "🌙" : "☀️";
    themeIcon.title =
      theme === "light" ? "تفعيل الوضع الليلي" : "تفعيل الوضع النهاري";
  }

  // تحديث meta theme-color للمتصفحات التي تدعمها
  updateMetaThemeColor(theme);
}

function updateMetaThemeColor(theme) {
  let metaThemeColor = document.querySelector('meta[name="theme-color"]');
  if (!metaThemeColor) {
    metaThemeColor = document.createElement("meta");
    metaThemeColor.name = "theme-color";
    document.head.appendChild(metaThemeColor);
  }
  metaThemeColor.content = theme === "dark" ? "#121212" : "#007bff";
}

function showThemeNotification(theme) {
  // إنشاء إشعار بصري صغير
  const notification = document.createElement("div");
  notification.className = `theme-notification alert alert-${
    theme === "dark" ? "info" : "warning"
  }`;
  notification.innerHTML = `
        <span>تم تفعيل الوضع ${theme === "dark" ? "الليلي" : "النهاري"}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
  notification.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 1060;
        min-width: 300px;
        animation: slideInRight 0.3s ease;
    `;

  document.body.appendChild(notification);

  // إزالة الإشعار بعد 3 ثوان
  setTimeout(() => {
    if (notification.parentNode) {
      notification.style.animation = "slideOutRight 0.3s ease";
      setTimeout(() => notification.remove(), 300);
    }
  }, 3000);
}

// ===== نظام العودة للأعلى =====
function initializeScrollToTop() {
  console.log("⬆️ تهيئة زر العودة للأعلى...");

  const scrollButton = document.getElementById("scrollToTop");
  if (!scrollButton) {
    console.warn("⚠️ زر العودة للأعلى غير موجود");
    return;
  }

  // التحكم في ظهور الزر عند التمرير
  window.addEventListener("scroll", function () {
    if (window.pageYOffset > 300) {
      scrollButton.classList.add("show");
    } else {
      scrollButton.classList.remove("show");
    }
  });

  // إضافة مستمع حدث للنقر
  scrollButton.addEventListener("click", function () {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });

  console.log("✅ زر العودة للأعلى جاهز");
}

// ===== نظام الفعاليات والحجوزات =====
// ...existing code...

function initializeBookingSystem() {
  // تهيئة أزرار الحجز
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("book-event")) {
      const eventId = e.target.getAttribute("data-event-id");
      const eventTitle = e.target.getAttribute("data-event-title");

      if (eventId && eventTitle) {
        openBookingModal(eventId, eventTitle);
      }
    }
  });

  // تهيئة نموذج الحجز
  const bookingForm = document.getElementById("bookingForm");
  if (bookingForm) {
    bookingForm.addEventListener("submit", handleBookingSubmit);
  }
}

function initializeEventActions() {
  // تهيئة أزرار المشاركة
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("share-event")) {
      shareEvent(e.target);
    }

    if (e.target.classList.contains("calendar-event")) {
      addToCalendar(e.target);
    }
  });
}

function loadEventsData() {
  // محاكاة تحميل البيانات (في التطبيق الحقيقي ستكون من API)
  console.log("📥 جاري تحميل بيانات الفعاليات...");

  // يمكن إضافة AJAX call هنا لتحميل البيانات الحقيقية
  setTimeout(() => {
    console.log("✅ تم تحميل بيانات الفعاليات");
  }, 500);
}

function openBookingModal(eventId, eventTitle) {
  console.log("🎫 فتح نموذج حجز الفعالية:", eventTitle);

  const modalElement = document.getElementById("bookingModal");
  if (!modalElement) {
    console.error("❌ نموذج الحجز غير موجود");
    return;
  }

  // تعيين بيانات الفعالية
  document.getElementById("bookingEventTitle").textContent = eventTitle;
  document.getElementById("eventId").value = eventId;

  // فتح المودال
  const modal = new bootstrap.Modal(modalElement);
  modal.show();
}

async function handleBookingSubmit(e) {
  e.preventDefault();

  console.log("📨 معالجة طلب الحجز...");

  const form = e.target;
  const formData = new FormData(form);
  const submitButton = form.querySelector('button[type="submit"]');
  const originalText = submitButton ? submitButton.innerHTML : "تأكيد الحجز";

  if (submitButton) {
    submitButton.disabled = true;
    submitButton.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status"></span> جاري الحجز...';
  }

  try {
    // إرسال بيانات الحجز إلى الخادم
    const resp = await fetch('book_event.php', {
      method: 'POST',
      body: formData,
      headers: {
        // Let browser set Content-Type for FormData; accept JSON response
        'Accept': 'application/json'
      }
    });

    // حاول تحليل JSON من الاستجابة
    let data = null;
    try {
      data = await resp.json();
    } catch (parseErr) {
      console.error('Failed to parse JSON response', parseErr);
    }

  if (resp.ok && data?.success) {
      showAlert(data.message || "تم الحجز بنجاح! سنتواصل معك قريباً.", "success");

      // إغلاق المودال
      const modal = bootstrap.Modal.getInstance(
        document.getElementById("bookingModal")
      );
      if (modal) modal.hide();

      // إعادة تعيين النموذج
      form.reset();
    } else {
      const msg = data?.message || 'حدث خطأ أثناء الحجز. حاول مرة أخرى.';
      showAlert(msg, 'danger');
      console.error('Booking failed', resp.status, data);
    }
  } catch (error) {
    console.error("❌ خطأ في الحجز:", error);
    showAlert("خطأ في الاتصال بالخادم. يرجى المحاولة مرة أخرى.", "danger");
  } finally {
    if (submitButton) {
      submitButton.disabled = false;
      submitButton.innerHTML = originalText;
    }
  }
}

function simulateBookingRequest(formData) {
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      // محاكاة نجاح أو فشل عشوائي للاختبار
      if (Math.random() > 0.1) {
        // 90% نجاح
        resolve({ success: true, message: "تم الحجز بنجاح" });
      } else {
        reject(new Error("فشل في الاتصال بالخادم"));
      }
    }, 1500);
  });
}

function shareEvent(button) {
  const eventId = button.getAttribute("data-event-id");
  const eventTitle = button.getAttribute("data-event-title");
  const eventUrl = `${window.location.origin}/event.php?id=${eventId}`;

  if (navigator.share) {
    navigator
      .share({
        title: eventTitle,
        text: "تفضل بمشاهدة هذه الفعالية المميزة",
        url: eventUrl,
      })
      .then(() => {
        console.log("✅ تمت المشاركة بنجاح");
      })
      .catch((error) => {
        console.log("❌ تم إلغاء المشاركة:", error);
      });
  } else {
    // نسخ الرابط إلى الحافظة
    navigator.clipboard
      .writeText(eventUrl)
      .then(() => {
        showAlert("تم نسخ رابط الفعالية إلى الحافظة", "success");
      })
      .catch(() => {
        // Fallback للنصوص القديمة
        prompt("انسخ الرابط التالي:", eventUrl);
      });
  }
}

function addToCalendar(button) {
  const eventDate = button.getAttribute("data-event-date");
  const eventTitle = button.getAttribute("data-event-title");
  const eventLocation = button.getAttribute("data-event-location");

  // إنشاء رابط تقويم Google
  const startDate = new Date(eventDate).toISOString().replace(/-|:|\.\d+/g, "");
  const endDate = new Date(new Date(eventDate).getTime() + 2 * 60 * 60 * 1000)
    .toISOString()
    .replace(/-|:|\.\d+/g, "");

  const calendarUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&dates=${startDate}/${endDate}&text=${encodeURIComponent(
    eventTitle
  )}&location=${encodeURIComponent(eventLocation)}`;

  window.open(calendarUrl, "_blank");
}

// ===== نظام البحث والتصفية =====
function initializeSearchFilter() {
  console.log("🔍 تهيئة نظام البحث والتصفية...");

  const searchInput = document.getElementById("searchInput");
  const categoryFilter = document.getElementById("categoryFilter");
  const dateFilter = document.getElementById("dateFilter");

  if (searchInput) {
    searchInput.addEventListener("input", debounce(filterEvents, 300));
  }

  if (categoryFilter) {
    categoryFilter.addEventListener("change", filterEvents);
  }

  if (dateFilter) {
    dateFilter.addEventListener("change", filterEvents);
  }

  console.log("✅ نظام البحث والتصفية جاهز");
}

function filterEvents() {
  const searchTerm = (
    document.getElementById("searchInput")?.value || ""
  ).toLowerCase();
  const category = document.getElementById("categoryFilter")?.value || "";
  const date = document.getElementById("dateFilter")?.value || "";

  const eventCards = document.querySelectorAll(".event-card");
  let visibleCount = 0;

  eventCards.forEach((card) => {
    const title =
      card.querySelector(".card-title")?.textContent.toLowerCase() || "";
    const description =
      card.querySelector(".card-text")?.textContent.toLowerCase() || "";
    const cardCategory = card.getAttribute("data-category") || "";
    const cardDate = card.getAttribute("data-date")?.split(" ")[0] || "";

    const matchesSearch =
      title.includes(searchTerm) || description.includes(searchTerm);
    const matchesCategory = !category || cardCategory === category;
    const matchesDate = !date || cardDate === date;

    if (matchesSearch && matchesCategory && matchesDate) {
      card.style.display = "block";
      card.classList.add("fade-in");
      visibleCount++;
    } else {
      card.style.display = "none";
    }
  });

  // عرض رسالة إذا لم توجد نتائج
  showNoResultsMessage(visibleCount === 0);
}

function showNoResultsMessage(show) {
  let message = document.getElementById("noResultsMessage");

  if (show && !message) {
    message = document.createElement("div");
    message.id = "noResultsMessage";
    message.className = "col-12 text-center py-5 fade-in";
    message.innerHTML = `
            <div class="alert alert-info">
                <h4>لا توجد نتائج</h4>
                <p>لم نعثر على فعاليات تطابق معايير البحث الخاصة بك.</p>
                <button onclick="clearFilters()" class="btn btn-primary">مسح الفلاتر</button>
            </div>
        `;
    document.getElementById("eventsList").appendChild(message);
  } else if (!show && message) {
    message.remove();
  }
}

function clearFilters() {
  const searchInput = document.getElementById("searchInput");
  const categoryFilter = document.getElementById("categoryFilter");
  const dateFilter = document.getElementById("dateFilter");

  if (searchInput) searchInput.value = "";
  if (categoryFilter) categoryFilter.value = "";
  if (dateFilter) dateFilter.value = "";

  filterEvents();
  showAlert("تم مسح جميع الفلاتر", "info");
}

// ===== نموذج الاتصال =====
function initializeContactForm() {
  console.log("📞 تهيئة نموذج الاتصال...");

  const contactForm = document.getElementById("contactForm");
  if (!contactForm) return;

  contactForm.addEventListener("submit", function (e) {
    if (!validateContactForm()) {
      e.preventDefault();
    } else {
      // إظهار حالة التحميل
      const submitButton = this.querySelector('button[type="submit"]');
      const originalText = submitButton.innerHTML;
      submitButton.disabled = true;
      submitButton.innerHTML =
        '<span class="spinner-border spinner-border-sm"></span> جاري الإرسال...';

      // إعادة التمكين بعد ثانية (في التطبيق الحقيقي سيكون بعد response)
      setTimeout(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
      }, 2000);
    }
  });

  console.log("✅ نموذج الاتصال جاهز");
}

function validateContactForm() {
  const name = document.getElementById("name")?.value.trim();
  const email = document.getElementById("email")?.value.trim();
  const message = document.getElementById("message")?.value.trim();

  // إعادة تعيين الأخطاء
  document.querySelectorAll(".is-invalid").forEach((el) => {
    el.classList.remove("is-invalid");
  });

  let isValid = true;

  if (!name) {
    document.getElementById("name")?.classList.add("is-invalid");
    isValid = false;
  }

  if (!email || !isValidEmail(email)) {
    document.getElementById("email")?.classList.add("is-invalid");
    isValid = false;
  }

  if (!message) {
    document.getElementById("message")?.classList.add("is-invalid");
    isValid = false;
  }

  return isValid;
}

// ===== نظام الحركات والتحسينات =====
function initializeAnimations() {
  console.log("✨ تهيئة الحركات والتحسينات...");

  // إضافة تأثيرات للعناصر عند التمرير
  const animatedElements = document.querySelectorAll(
    ".fade-in, .slide-in-left, .slide-in-right"
  );

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = "1";
          entry.target.style.transform = "translateY(0) translateX(0)";
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1 }
  );

  animatedElements.forEach((el) => {
    el.style.opacity = "0";
    el.style.transform = "translateY(30px)";
    if (el.classList.contains("slide-in-left")) {
      el.style.transform = "translateX(-50px)";
    } else if (el.classList.contains("slide-in-right")) {
      el.style.transform = "translateX(50px)";
    }
    observer.observe(el);
  });

  console.log("✅ الحركات والتحسينات جاهزة");
}

// ===== وظائف مساعدة =====
function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

function showAlert(message, type = "info") {
  // إنشاء عنصر التنبيه
  const alertDiv = document.createElement("div");
  alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
  alertDiv.style.cssText = `
        position: fixed;
        top: 90px;
        right: 20px;
        z-index: 1060;
        min-width: 300px;
        animation: slideInRight 0.3s ease;
    `;
  alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

  // إضافة التنبيه إلى الصفحة
  document.body.appendChild(alertDiv);

  // إزالة التنبيه تلقائيًا بعد 5 ثوان
  setTimeout(() => {
    if (alertDiv.parentNode) {
      alertDiv.style.animation = "slideOutRight 0.3s ease";
      setTimeout(() => alertDiv.remove(), 300);
    }
  }, 5000);
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("ar-SA", {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

// ===== جعل الوظائف متاحة globally =====
window.toggleTheme = function () {
  const currentTheme = localStorage.getItem("theme") || "light";
  const newTheme = currentTheme === "light" ? "dark" : "light";
  applyTheme(newTheme);
  localStorage.setItem("theme", newTheme);
};

window.clearFilters = clearFilters;
window.openBookingModal = openBookingModal;
window.shareEvent = shareEvent;
window.addToCalendar = addToCalendar;

// ===== إضافة أنماط CSS للحركات =====
const style = document.createElement("style");
style.textContent = `
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .theme-notification {
        animation: slideInRight 0.3s ease;
    }
    
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
`;
document.head.appendChild(style);

console.log("🎉 تم تحميل main.js بنجاح!");

// في دالة initializeEventSystem، أضف تحميل السلايدر
function initializeEventSystem() {
  console.log("🎪 تهيئة نظام الفعاليات...");

  initializeBookingSystem();
  initializeEventActions();
  loadEventsData();
  initializeFeaturedEventsCarousel(); // ← إضافة هذه السطر
}

// دالة تهيئة سلايدر الفعاليات البارزة
function initializeFeaturedEventsCarousel() {
  console.log("🔄 تهيئة سلايدر الفعاليات البارزة...");

  const carousel = document.getElementById("featuredEventsCarousel");
  if (!carousel) {
    console.warn("⚠️ سلايدر الفعاليات البارزة غير موجود");
    return;
  }

  // إضافة تأثيرات تفاعلية
  const carouselItems = carousel.querySelectorAll(".carousel-item");
  carouselItems.forEach((item, index) => {
    // إضافة تأثير عند التمرير
    item.addEventListener("mouseenter", function () {
      this.style.transform = "scale(1.02)";
    });

    item.addEventListener("mouseleave", function () {
      this.style.transform = "scale(1)";
    });
  });

  // التحكم التلقائي في السلايدر
  let autoSlide = setInterval(() => {
    const nextButton = carousel.querySelector(".carousel-control-next");
    if (nextButton) {
      nextButton.click();
    }
  }, 5000); // التبديل كل 5 ثواني

  // إيقاف التبديل التلقائي عند التوقف على السلايدر
  carousel.addEventListener("mouseenter", () => {
    clearInterval(autoSlide);
  });

  carousel.addEventListener("mouseleave", () => {
    autoSlide = setInterval(() => {
      const nextButton = carousel.querySelector(".carousel-control-next");
      if (nextButton) {
        nextButton.click();
      }
    }, 5000);
  });

  console.log("✅ سلايدر الفعاليات البارزة جاهز");
}

// دالة لتحميل الفعاليات البارزة عبر AJAX (اختياري)
function loadFeaturedEventsViaAjax() {
  fetch("api/get_featured_events.php")
    .then((response) => response.json())
    .then((events) => {
      displayFeaturedEvents(events);
    })
    .catch((error) => {
      console.error("❌ خطأ في تحميل الفعاليات البارزة:", error);
    });
}

// دالة لعرض الفعاليات البارزة (للاستخدام مع AJAX)
function displayFeaturedEvents(events) {
  const carouselInner = document.querySelector(
    "#featuredEventsCarousel .carousel-inner"
  );
  const carouselIndicators = document.querySelector(
    "#featuredEventsCarousel .carousel-indicators"
  );

  if (!carouselInner || !events.length) return;

  // مسح المحتوى الحالي
  carouselInner.innerHTML = "";
  carouselIndicators.innerHTML = "";

  // إضافة الشرائح الجديدة
  events.forEach((event, index) => {
    const isActive = index === 0 ? "active" : "";

    // إضافة indicator
    if (carouselIndicators) {
      const indicator = document.createElement("button");
      indicator.type = "button";
      indicator.dataset.bsTarget = "#featuredEventsCarousel";
      indicator.dataset.bsSlideTo = index;
      indicator.className = isActive ? "active" : "";
      indicator.setAttribute("aria-label", `Slide ${index + 1}`);
      carouselIndicators.appendChild(indicator);
    }

    // إضافة slide
    const slide = document.createElement("div");
    slide.className = `carousel-item ${isActive}`;
    slide.innerHTML = `
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="${event.image || "assets/img/default-event.jpg"}" 
                        class="d-block w-100 rounded-3" 
                        alt="${event.title}"
                        style="height: 400px; object-fit: cover;">
                </div>
                <div class="col-md-6">
                    <div class="carousel-content p-4">
                        <h3 class="text-primary">${event.title}</h3>
                        <p class="lead">${event.description.substring(
                          0,
                          150
                        )}...</p>
                        <div class="event-info mb-3">
                            <p class="mb-1"><strong>📅 التاريخ:</strong> ${
                              event.event_date
                            }</p>
                            <p class="mb-1"><strong>📍 المكان:</strong> ${
                              event.location
                            }</p>
                            <p class="mb-1"><strong>🏷️ التصنيف:</strong> ${
                              event.category
                            }</p>
                        </div>
                        <div class="carousel-buttons">
                            <a href="event.php?id=${
                              event.id
                            }" class="btn btn-primary me-2">عرض التفاصيل</a>
                            <button class="btn btn-success book-event" 
                                    data-event-id="${event.id}" 
                                    data-event-title="${event.title}">
                                احجز الآن
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    carouselInner.appendChild(slide);
  });

  // إعادة تهيئة السلايدر
  initializeFeaturedEventsCarousel();
}
