<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مواد التسويق - Lumi Cashier</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-gradient-to-br from-purple-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-full mb-6 shadow-lg">
                    <span class="text-4xl font-bold text-white">L</span>
                </div>
                <h1 class="text-4xl font-bold text-gray-800 mb-4">مواد التسويق</h1>
                <p class="text-xl text-gray-600">ملفات جاهزة للطباعة والمشاركة مع العملاء</p>
            </div>

            <!-- Marketing Brochure Card -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden hover:shadow-3xl transition-all duration-300">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-8 text-white">
                    <div class="flex items-center gap-4 mb-4">
                        <i class="bi bi-file-pdf text-5xl"></i>
                        <div>
                            <h2 class="text-3xl font-bold">البروشور التسويقي</h2>
                            <p class="text-purple-100">دليل شامل عن نظام Lumi Cashier</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid md:grid-cols-2 gap-6 mb-8">
                        <div class="flex items-start gap-3">
                            <i class="bi bi-check-circle-fill text-green-500 text-2xl"></i>
                            <div>
                                <h3 class="font-bold text-gray-800 mb-1">نبذة شاملة عن النظام</h3>
                                <p class="text-gray-600 text-sm">كل المعلومات اللي العميل محتاجها</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="bi bi-check-circle-fill text-green-500 text-2xl"></i>
                            <div>
                                <h3 class="font-bold text-gray-800 mb-1">المميزات التفصيلية</h3>
                                <p class="text-gray-600 text-sm">شرح كل ميزة بالتفصيل</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="bi bi-check-circle-fill text-green-500 text-2xl"></i>
                            <div>
                                <h3 class="font-bold text-gray-800 mb-1">دليل الاستخدام</h3>
                                <p class="text-gray-600 text-sm">خطوات واضحة وسهلة</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="bi bi-check-circle-fill text-green-500 text-2xl"></i>
                            <div>
                                <h3 class="font-bold text-gray-800 mb-1">تصميم احترافي</h3>
                                <p class="text-gray-600 text-sm">جاهز للطباعة والمشاركة</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-6 mb-8">
                        <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="bi bi-info-circle text-purple-600"></i>
                            المحتويات:
                        </h3>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-purple-600 rounded-full"></span>
                                صفحة غلاف احترافية
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-purple-600 rounded-full"></span>
                                نبذة عن النظام ومميزاته
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-purple-600 rounded-full"></span>
                                شرح تفصيلي لكل الميزات
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-purple-600 rounded-full"></span>
                                دليل خطوة بخطوة للاستخدام
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-purple-600 rounded-full"></span>
                                نصائح للاستخدام الأمثل
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-purple-600 rounded-full"></span>
                                معلومات التواصل
                            </li>
                        </ul>
                    </div>

                    <div class="flex gap-4">
                        <a href="{{ route('marketing.brochure') }}" target="_blank"
                           class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 text-center flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
                            <i class="bi bi-eye text-xl"></i>
                            <span>عرض البروشور</span>
                        </a>

                        <a href="{{ route('marketing.brochure.download') }}"
                           class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 text-center flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
                            <i class="bi bi-download text-xl"></i>
                            <span>تحميل PDF</span>
                        </a>
                    </div>

                    <div class="mt-6 p-4 bg-yellow-50 border-r-4 border-yellow-400 rounded-lg">
                        <div class="flex items-start gap-3">
                            <i class="bi bi-lightbulb text-yellow-600 text-xl"></i>
                            <div>
                                <h4 class="font-bold text-gray-800 mb-1">نصيحة:</h4>
                                <p class="text-gray-700 text-sm">
                                    اطبع البروشور ده ووزعه على العملاء المحتملين، 
                                    أو ابعته ليهم عبر الواتساب والإيميل. 
                                    كل المعلومات موجودة بشكل واضح واحترافي!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center mt-8">
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-800 font-semibold transition-colors">
                    <i class="bi bi-arrow-right"></i>
                    <span>رجوع للصفحة الرئيسية</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
