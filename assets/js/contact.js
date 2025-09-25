// تحقق من نموذج الاتصال
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // إعادة تعيين الصفوف غير صالحة
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            
            // التحقق من الحقول
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const message = document.getElementById('message').value.trim();
            
            let isValid = true;
            
            if (!name) {
                document.getElementById('name').classList.add('is-invalid');
                isValid = false;
            }
            
            if (!email || !validateEmail(email)) {
                document.getElementById('email').classList.add('is-invalid');
                isValid = false;
            }
            
            if (!message) {
                document.getElementById('message').classList.add('is-invalid');
                isValid = false;
            }
            
            if (isValid) {
                // إظهار رسالة تحميل
                const submitBtn = contactForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '⏳ جاري الإرسال...';
                submitBtn.disabled = true;
                
                // محاكاة الإرسال (في الواقع سيرسل النموذج تلقائياً)
                setTimeout(() => {
                    contactForm.submit();
                }, 1000);
            }
        });
    }
});

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}