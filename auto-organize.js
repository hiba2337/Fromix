// auto-organize.js
// هذا السكريبت ينظم كل شيء تلقائياً!

const fs = require('fs');
const path = require('path');

console.log('🚀 بدء التنظيم التلقائي للمشروع...\n');

// خطوة 1: تنظيم الصور
console.log('📸 الخطوة 1: تنظيم الصور...');

const imageCategories = {
  blog: ['blog01', 'blog02', 'blog03', 'blog04', 'blog05', 'blog06', 'blog07', 'blog08', 'blog09'],
  courses: ['course01', 'course02', 'course03', 'course04', 'course05', 'course06', 'web dev-courses', 'graduation-diploma-certificate-graduation-hat'],
  events: ['event01', 'event02', 'event03', 'event04', 'event05', 'event06', 'event07', 'event08'],
  landing: ['landing', 'landing1', 'landing03', 'landing04', 'contact', 'desktop-table-office', 'coworkers-team-working-brainstorming-concept'],
  partners: ['partner05', 'partners01', 'partners02', 'partners03'],
  testimonials: ['test2', 'testi1', 'testi3', 'testi4', 'testi5', 'testi6'],
  icons: ['recommendations-icon']
};

function organizeImages() {
  const imagesDir = 'assets/images';
  
  for (const [category, fileNames] of Object.entries(imageCategories)) {
    const categoryDir = path.join(imagesDir, category);
    
    // إنشاء المجلد إذا لم يكن موجوداً
    if (!fs.existsSync(categoryDir)) {
      fs.mkdirSync(categoryDir, { recursive: true });
    }
    
    // نقل الصور
    fileNames.forEach(fileName => {
      // البحث عن الملف بأي امتداد
      const files = fs.readdirSync(imagesDir).filter(f => 
        f.startsWith(fileName) && !fs.statSync(path.join(imagesDir, f)).isDirectory()
      );
      
      files.forEach(file => {
        const oldPath = path.join(imagesDir, file);
        const newPath = path.join(categoryDir, file);
        
        try {
          if (fs.existsSync(oldPath) && !fs.existsSync(newPath)) {
            fs.renameSync(oldPath, newPath);
            console.log(`  ✅ نقل: ${file} → ${category}/`);
          }
        } catch (error) {
          console.log(`  ⚠️  تخطي: ${file} (${error.message})`);
        }
      });
    });
  }
  
  console.log('✅ اكتمل تنظيم الصور!\n');
}

// خطوة 2: تحديث روابط HTML
console.log('🔗 الخطوة 2: تحديث روابط HTML...');

const pathUpdatesForPages = {
  'href="css/blog.css': 'href="../assets/css/pages/blog.css',
  'href="css/evets.css': 'href="../assets/css/pages/evets.css',
  'href="css/formation.css': 'href="../assets/css/pages/formation.css',
  'href="css/from-details.css': 'href="../assets/css/pages/from-details.css',
  'href="css/login.css': 'href="../assets/css/pages/login.css',
  'href="css/paiement.css': 'href="../assets/css/pages/paiement.css',
  'href="css/sign-up.css': 'href="../assets/css/pages/sign-up.css',
  'href="css/normilze.css': 'href="../assets/css/normilze.css',
  'href="assets/css/blog.css': 'href="../assets/css/pages/blog.css',
  'href="assets/css/evets.css': 'href="../assets/css/pages/evets.css',
  'href="assets/css/formation.css': 'href="../assets/css/pages/formation.css',
  'href="assets/css/from-details.css': 'href="../assets/css/pages/from-details.css',
  'href="assets/css/login.css': 'href="../assets/css/pages/login.css',
  'href="assets/css/paiement.css': 'href="../assets/css/pages/paiement.css',
  'href="assets/css/sign-up.css': 'href="../assets/css/pages/sign-up.css',
  'src="images/': 'src="../assets/images/',
  'src="assets/images/': 'src="../assets/images/',
  'href="index.html': 'href="../index.html',
};

const pathUpdatesForIndex = {
  'href="css/index.css': 'href="assets/css/pages/index.css',
  'href="css/normilze.css': 'href="assets/css/normilze.css',
  'href="assets/css/index.css': 'href="assets/css/pages/index.css',
  'src="images/': 'src="assets/images/',
  'href="Blog.html': 'href="pages/Blog.html',
  'href="eventement.html': 'href="pages/eventement.html',
  'href="formation.html': 'href="pages/formation.html',
  'href="fromation-details.html': 'href="pages/fromation-details.html',
  'href="login.html': 'href="pages/login.html',
  'href="sign-up.html': 'href="pages/sign-up.html',
  'href="paiement.html': 'href="pages/paiement.html',
  'href="panier.html': 'href="pages/panier.html',
  'href="dashboard.html': 'href="pages/dashboard.html',
};

function updateHTMLFile(filePath, updates) {
  try {
    if (!fs.existsSync(filePath)) {
      console.log(`  ⚠️  الملف غير موجود: ${filePath}`);
      return false;
    }
    
    let content = fs.readFileSync(filePath, 'utf8');
    let updatedContent = content;
    
    for (const [oldPath, newPath] of Object.entries(updates)) {
      updatedContent = updatedContent.split(oldPath).join(newPath);
    }
    
    fs.writeFileSync(filePath, updatedContent, 'utf8');
    console.log(`  ✅ تم تحديث: ${filePath}`);
    return true;
  } catch (error) {
    console.error(`  ❌ خطأ في ${filePath}:`, error.message);
    return false;
  }
}

function updateAllHTML() {
  // تحديث index.html
  updateHTMLFile('index.html', pathUpdatesForIndex);
  
  // تحديث صفحات pages/
  const pagesDir = 'pages';
  if (fs.existsSync(pagesDir)) {
    const htmlFiles = fs.readdirSync(pagesDir).filter(f => f.endsWith('.html'));
    htmlFiles.forEach(file => {
      updateHTMLFile(path.join(pagesDir, file), pathUpdatesForPages);
    });
  }
  
  console.log('✅ اكتمل تحديث HTML!\n');
}

// خطوة 3: تحديث مسارات CSS
console.log('🎨 الخطوة 3: تحديث مسارات CSS...');

function updateCSSFiles() {
  const cssDir = 'assets/css/pages';
  
  if (!fs.existsSync(cssDir)) {
    console.log('  ⚠️  مجلد CSS/pages غير موجود');
    return;
  }
  
  const cssFiles = fs.readdirSync(cssDir).filter(f => f.endsWith('.css'));
  
  cssFiles.forEach(file => {
    const filePath = path.join(cssDir, file);
    try {
      let content = fs.readFileSync(filePath, 'utf8');
      
      let updatedContent = content
        .replace(/url\(['"]?\.\.\/images\//g, "url('../../images/")
        .replace(/url\(['"]?images\//g, "url('../../images/")
        .replace(/url\(['"]?\.\.\/\.\.\/images\//g, "url('../../images/")
        .replace(/url\(['"]?\.\.\/\.\.\/\.\.\/images\//g, "url('../../images/");
      
      fs.writeFileSync(filePath, updatedContent, 'utf8');
      console.log(`  ✅ تم تحديث: ${file}`);
    } catch (error) {
      console.error(`  ❌ خطأ في ${file}:`, error.message);
    }
  });
  
  console.log('✅ اكتمل تحديث CSS!\n');
}

// تشغيل كل الخطوات
organizeImages();
updateAllHTML();
updateCSSFiles();

console.log('✨✨✨ اكتمل التنظيم التلقائي بنجاح! ✨✨✨');
console.log('\n📋 الخطوات التالية:');
console.log('1. افتح index.html في المتصفح للاختبار');
console.log('2. تحقق من الصور والأنماط');
console.log('3. اختبر الروابط بين الصفحات');
console.log('4. افتح Developer Tools (F12) وتحقق من عدم وجود أخطاء');