<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام Lumi Cashier - دليل شامل</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #1f2937;
            background: white;
        }

        .cover-page {
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 40px;
            page-break-after: always;
        }

        .logo {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .cover-title {
            font-size: 42px;
            font-weight: bold;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .cover-subtitle {
            font-size: 20px;
            margin-bottom: 40px;
            opacity: 0.95;
        }

        .cover-features {
            display: flex;
            gap: 30px;
            margin-top: 40px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .cover-feature {
            background: rgba(255,255,255,0.15);
            padding: 15px 25px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            font-size: 14px;
        }

        .page {
            padding: 40px;
            min-height: 100vh;
            page-break-after: always;
        }

        .page-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 28px;
            color: #667eea;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .page-subtitle {
            font-size: 14px;
            color: #6b7280;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 20px;
            color: #1f2937;
            font-weight: bold;
            margin-bottom: 15px;
            padding-right: 15px;
            border-right: 4px solid #667eea;
        }

        .feature-grid {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .feature-row {
            display: table-row;
        }

        .feature-item {
            display: table-cell;
            width: 50%;
            padding: 15px;
            vertical-align: top;
        }

        .feature-card {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            border-radius: 10px;
            padding: 20px;
            height: 100%;
            border-right: 4px solid #667eea;
        }

        .feature-icon {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            color: white;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .feature-name {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .feature-desc {
            font-size: 11px;
            color: #4b5563;
            line-height: 1.5;
        }

        .step-list {
            counter-reset: step-counter;
            list-style: none;
        }

        .step-item {
            counter-increment: step-counter;
            margin-bottom: 20px;
            padding-right: 60px;
            position: relative;
        }

        .step-item:before {
            content: counter(step-counter);
            position: absolute;
            right: 0;
            top: 0;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            color: white;
            text-align: center;
            line-height: 40px;
            font-weight: bold;
            font-size: 18px;
        }

        .step-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .step-desc {
            font-size: 11px;
            color: #4b5563;
            line-height: 1.6;
        }

        .highlight-box {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-right: 4px solid #f59e0b;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .highlight-title {
            font-size: 16px;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 10px;
        }

        .highlight-text {
            font-size: 11px;
            color: #78350f;
            line-height: 1.6;
        }

        .footer {
            text-align: center;
            padding: 30px;
            background: #f9fafb;
            border-top: 3px solid #667eea;
            margin-top: 40px;
        }

        .footer-text {
            font-size: 12px;
            color: #6b7280;
        }

        .contact-info {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .contact-item {
            margin-bottom: 10px;
            font-size: 12px;
        }

        @page {
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Cover Page -->
    <div class="cover-page">
        <div class="logo">L</div>
        <h1 class="cover-title">نظام Lumi Cashier</h1>
        <p class="cover-subtitle">نظام نقاط البيع الاحترافي - حل متكامل لإدارة أعمالك</p>
        
        <div class="cover-features">
            <div class="cover-feature">✨ سهل الاستخدام</div>
            <div class="cover-feature">⚡ سريع وفعّال</div>
            <div class="cover-feature">📊 تقارير شاملة</div>
            <div class="cover-feature">🖨️ طباعة احترافية</div>
            <div class="cover-feature">🌐 متعدد اللغات</div>
        </div>
    </div>

    <!-- Page 1: Overview -->
    <div class="page">
        <div class="page-header">
            <h2 class="page-title">نبذة عن النظام</h2>
            <p class="page-subtitle">حل شامل لإدارة نقاط البيع والمخزون</p>
        </div>

        <div class="section">
            <h3 class="section-title">ليه تختار Lumi Cashier؟</h3>
            <p style="margin-bottom: 20px; line-height: 1.8;">
                نظام Lumi Cashier هو حل احترافي متكامل لإدارة نقاط البيع، مصمم خصيصاً للأعمال الصغيرة والمتوسطة.
                النظام يجمع بين السهولة في الاستخدام والقوة في الأداء، مع واجهة عربية بالكامل وميزات متقدمة.
            </p>

            <div class="highlight-box">
                <div class="highlight-title">🎯 مصمم خصيصاً للسوق المصري</div>
                <div class="highlight-text">
                    النظام متوافق 100% مع طبيعة العمل في مصر، بيدعم العملات المصرية، الضرائب المحلية، 
                    وطرق الدفع المختلفة. كل حاجة بالعربي وسهل تتعامل معاها!
                </div>
            </div>
        </div>

        <div class="section">
            <h3 class="section-title">المميزات الرئيسية</h3>
            
            <div class="feature-grid">
                <div class="feature-row">
                    <div class="feature-item">
                        <div class="feature-card">
                            <div class="feature-icon">🛒</div>
                            <div class="feature-name">نقطة البيع (POS)</div>
                            <div class="feature-desc">
                                واجهة بيع سريعة وسهلة، بتخليك تخلص العملاء في ثواني. 
                                بحث ذكي عن المنتجات، حساب تلقائي للضرائب والخصومات.
                            </div>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-card">
                            <div class="feature-icon">📦</div>
                            <div class="feature-name">إدارة المخزون</div>
                            <div class="feature-desc">
                                تحكم كامل في المنتجات والأصناف. متابعة الكميات، 
                                تنبيهات للنواقص، وإدارة متقدمة للأسعار.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="feature-row">
                    <div class="feature-item">
                        <div class="feature-card">
                            <div class="feature-icon">📊</div>
                            <div class="feature-name">تقارير شاملة</div>
                            <div class="feature-desc">
                                تقارير مبيعات يومية وشهرية، تحليل الأرباح، 
                                أكثر المنتجات مبيعاً، وكل البيانات اللي محتاجها.
                            </div>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-card">
                            <div class="feature-icon">🖨️</div>
                            <div class="feature-name">طباعة احترافية</div>
                            <div class="feature-desc">
                                فواتير حرارية احترافية، تقارير PDF قابلة للطباعة، 
                                وتصميمات جاهزة ومتوافقة مع كل الطابعات.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page 2: Features Details -->
    <div class="page">
        <div class="page-header">
            <h2 class="page-title">الميزات التفصيلية</h2>
            <p class="page-subtitle">كل حاجة محتاجها في نظام واحد</p>
        </div>

        <div class="section">
            <h3 class="section-title">🛒 نقطة البيع (POS)</h3>
            <ul style="margin-right: 20px; line-height: 2;">
                <li>واجهة سريعة وسهلة الاستخدام</li>
                <li>بحث ذكي عن المنتجات بالاسم أو الباركود</li>
                <li>حساب تلقائي للضرائب والخصومات</li>
                <li>دعم طرق دفع متعددة (كاش، فيزا، فودافون كاش، إلخ)</li>
                <li>إضافة ملاحظات للطلبات</li>
                <li>حفظ بيانات العملاء</li>
            </ul>
        </div>

        <div class="section">
            <h3 class="section-title">📦 إدارة المنتجات والمخزون</h3>
            <ul style="margin-right: 20px; line-height: 2;">
                <li>إضافة منتجات غير محدودة</li>
                <li>تصنيف المنتجات في فئات</li>
                <li>صور للمنتجات</li>
                <li>متابعة كميات المخزون</li>
                <li>تنبيهات عند نقص الكمية</li>
                <li>تحديد أسعار بيع وشراء</li>
                <li>تفعيل/تعطيل المنتجات</li>
            </ul>
        </div>

        <div class="section">
            <h3 class="section-title">📊 التقارير والإحصائيات</h3>
            <ul style="margin-right: 20px; line-height: 2;">
                <li>تقارير مبيعات يومية</li>
                <li>تقارير شهرية شاملة</li>
                <li>تحليل الأرباح والخسائر</li>
                <li>أكثر المنتجات مبيعاً</li>
                <li>تقارير المخزون</li>
                <li>تصدير التقارير PDF</li>
            </ul>
        </div>

        <div class="section">
            <h3 class="section-title">⚙️ الإعدادات</h3>
            <ul style="margin-right: 20px; line-height: 2;">
                <li>تخصيص معلومات المحل</li>
                <li>رفع شعار المحل</li>
                <li>إعدادات الضرائب</li>
                <li>تخصيص الفواتير</li>
                <li>دعم اللغة العربية والإنجليزية</li>
            </ul>
        </div>
    </div>

    <!-- Page 3: How to Use -->
    <div class="page">
        <div class="page-header">
            <h2 class="page-title">طريقة الاستخدام</h2>
            <p class="page-subtitle">خطوات بسيطة للبدء مع النظام</p>
        </div>

        <div class="section">
            <h3 class="section-title">البدء السريع</h3>
            
            <ol class="step-list">
                <li class="step-item">
                    <div class="step-title">تسجيل الدخول</div>
                    <div class="step-desc">
                        افتح النظام واستخدم بيانات الدخول الخاصة بيك. 
                        الواجهة سهلة ومباشرة، مش محتاج تدريب معقد.
                    </div>
                </li>

                <li class="step-item">
                    <div class="step-title">إعداد معلومات المحل</div>
                    <div class="step-desc">
                        روح على الإعدادات وحط معلومات محلك (الاسم، العنوان، الموبايل).
                        ارفع شعار محلك عشان يظهر في الفواتير.
                    </div>
                </li>

                <li class="step-item">
                    <div class="step-title">إضافة الفئات</div>
                    <div class="step-desc">
                        ابدأ بإنشاء فئات للمنتجات (مثلاً: مشروبات، أطعمة، إلكترونيات).
                        ده هيساعدك تنظم المنتجات وتلاقيها بسرعة.
                    </div>
                </li>

                <li class="step-item">
                    <div class="step-title">إضافة المنتجات</div>
                    <div class="step-desc">
                        ضيف منتجاتك واحد واحد: الاسم، السعر، الكمية، والصورة لو موجودة.
                        كل منتج بيتحط في فئة معينة عشان يسهل الوصول ليه.
                    </div>
                </li>

                <li class="step-item">
                    <div class="step-title">البدء في البيع</div>
                    <div class="step-desc">
                        روح على نقطة البيع (POS)، اختار المنتجات، حدد طريقة الدفع، واطبع الفاتورة.
                        كل حاجة تلقائية: الحساب، الضرائب، الخصومات.
                    </div>
                </li>

                <li class="step-item">
                    <div class="step-title">متابعة التقارير</div>
                    <div class="step-desc">
                        شوف مبيعاتك اليومية والشهرية من قسم التقارير.
                        اعرف أرباحك، أكثر المنتجات مبيعاً، وحالة المخزون.
                    </div>
                </li>
            </ol>
        </div>

        <div class="highlight-box">
            <div class="highlight-title">💡 نصيحة مهمة</div>
            <div class="highlight-text">
                اتأكد إنك بتسجل كل عملية بيع في النظام عشان التقارير تكون دقيقة.
                النظام بيحفظ كل حاجة تلقائياً، فمتقلقش على البيانات!
            </div>
        </div>
    </div>

    <!-- Page 4: Tips & Contact -->
    <div class="page">
        <div class="page-header">
            <h2 class="page-title">نصائح للاستخدام الأمثل</h2>
            <p class="page-subtitle">اعمل الأفضل من النظام</p>
        </div>

        <div class="section">
            <h3 class="section-title">✨ أفضل الممارسات</h3>
            
            <div style="margin-bottom: 15px; padding: 15px; background: #f3f4f6; border-radius: 8px;">
                <strong style="color: #667eea;">📋 تنظيم المنتجات:</strong>
                <p style="margin-top: 5px; font-size: 10px;">
                    استخدم فئات واضحة ومنطقية. كل ما المنتجات منظمة أكتر، 
                    كل ما البيع يكون أسرع.
                </p>
            </div>

            <div style="margin-bottom: 15px; padding: 15px; background: #f3f4f6; border-radius: 8px;">
                <strong style="color: #667eea;">💰 الأسعار الصحيحة:</strong>
                <p style="margin-top: 5px; font-size: 10px;">
                    دايماً تأكد من أسعار البيع والشراء. ده هيساعدك تحسب أرباحك بدقة.
                </p>
            </div>

            <div style="margin-bottom: 15px; padding: 15px; background: #f3f4f6; border-radius: 8px;">
                <strong style="color: #667eea;">📊 متابعة التقارير:</strong>
                <p style="margin-top: 5px; font-size: 10px;">
                    خصص وقت يومي لمراجعة التقارير. ده هيخليك فاهم أداء محلك كويس.
                </p>
            </div>

            <div style="margin-bottom: 15px; padding: 15px; background: #f3f4f6; border-radius: 8px;">
                <strong style="color: #667eea;">🔄 جرد المخزون:</strong>
                <p style="margin-top: 5px; font-size: 10px;">
                    اعمل جرد دوري للمخزون عشان تتأكد إن الكميات في النظام مظبوطة.
                </p>
            </div>

            <div style="margin-bottom: 15px; padding: 15px; background: #f3f4f6; border-radius: 8px;">
                <strong style="color: #667eea;">🖨️ إعدادات الطابعة:</strong>
                <p style="margin-top: 5px; font-size: 10px;">
                    اضبط إعدادات الطابعة الحرارية من أول مرة عشان الفواتير تطلع نضيفة.
                </p>
            </div>
        </div>

        <div class="section">
            <h3 class="section-title">🎯 المزايا التنافسية</h3>
            <ul style="margin-right: 20px; line-height: 2;">
                <li><strong>سرعة الأداء:</strong> النظام خفيف وسريع جداً</li>
                <li><strong>سهولة الاستخدام:</strong> ما بيحتاجش تدريب طويل</li>
                <li><strong>دعم كامل للعربية:</strong> كل حاجة بالعربي</li>
                <li><strong>تقارير احترافية:</strong> PDF جاهز للطباعة</li>
                <li><strong>أمان عالي:</strong> بياناتك محمية 100%</li>
            </ul>
        </div>

        <div class="contact-info">
            <h3 style="margin-bottom: 15px; font-size: 18px;">📞 للتواصل والدعم</h3>
            <div class="contact-item">
                <strong>📧 البريد الإلكتروني:</strong> support@lumicashier.com
            </div>
            <div class="contact-item">
                <strong>📱 الواتساب:</strong> +20 XXX XXX XXXX
            </div>
            <div class="contact-item">
                <strong>🌐 الموقع:</strong> www.lumicashier.com
            </div>
        </div>

        <div class="footer">
            <p class="footer-text">
                <strong>Lumi Cashier &copy; 2025</strong><br>
                جميع الحقوق محفوظة
            </p>
        </div>
    </div>

</body>
</html>
