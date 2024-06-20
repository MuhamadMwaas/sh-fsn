// public/js/darkmode-config.js

const options = {
    bottom: '32px', // راحة الزر المظلم أسفل الصفحة
    right: '32px', // راحة الزر المظلم على اليمين
    left: 'unset', // راحة الزر المظلم على اليسار
    time: '0.5s', // مدة الانزلاق
    mixColor: '#fff', // اللون الذي يظهر أثناء الانتقال من النور إلى الظلام
    backgroundColor: '#fff', // لون الخلفية أثناء الوضع النوري
    buttonColorDark: '#100f2c', // لون الزر عند تفعيل الوضع المظلم
    buttonColorLight: '#fff', // لون الزر عند تفعيل الوضع النوري
    saveInCookies: false, // حفظ تفضيلات الوضع في ملفات الكوكيز
    label: '🌓', // النص على الزر
    autoMatchOsTheme: true, // تطابق الوضع تلقائيا مع وضع النظام
};

const darkmode = new Darkmode(options);
darkmode.showWidget();
