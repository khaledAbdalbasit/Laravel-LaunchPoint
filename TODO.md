# LaunchPoint — TODO

## 🔴 أولوية عالية

- [ ] `launchpoint:make-model` — إنشاء Model مع `$fillable` جاهزة
  - [ ] `--migration` — إنشاء migration مع الـ model
  - [ ] `--factory` — إنشاء factory
  - [ ] `--seeder` — إنشاء seeder
  - [ ] `--all` — كل ما سبق دفعة واحدة

- [ ] `launchpoint:make-request` — إنشاء FormRequest منظم
  - [ ] `authorize()` + `rules()` + `messages()` جاهزين
  - [ ] `--model=` — يولد rules مبنية على اسم الـ model

- [ ] `launchpoint:make-resource` — إنشاء API Resource
  - [ ] `--collection` — يولد ResourceCollection معاه

- [ ] Pagination في `ApiResponseTrait`
  - [ ] دعم `LengthAwarePaginator` في الـ response
  - [ ] إضافة `meta` و `links` في الـ JSON

---

## 🟡 أولوية متوسطة

- [ ] `launchpoint:make-enum` — إنشاء PHP 8.1 Backed Enum
  - [ ] `--cases=` — تحديد الـ cases من الـ command مباشرة
  - [ ] `label()` method جاهزة

- [ ] `launchpoint:make-action` — Single Action Class
  - [ ] `handle()` method جاهزة

- [ ] `launchpoint:make-trait` — إنشاء Trait مع namespace صح

- [ ] `launchpoint:make-exception` — Custom Exception
  - [ ] `render()` method تُرجع JSON response
  - [ ] تلقائياً يُسجَّل في `bootstrap/app.php`

---

## 🟢 أولوية منخفضة / تحسينات

- [ ] `launchpoint:list` — جدول بكل الأوامر والـ options بتاعتهم

- [ ] `launchpoint:health` — فحص صحة المشروع
  - [ ] التحقق من اتصال الـ Database
  - [ ] التحقق من اكتمال ملف `.env`
  - [ ] التحقق من وجود المجلدات الأساسية

- [ ] `launchpoint:make-filter` — Query Filter Class للتصفية مع Eloquent

- [ ] `launchpoint:make-scope` — Eloquent Local Scope

---

## 🧪 Testing

- [ ] إعداد PHPUnit في الـ package
- [ ] Unit Tests لكل command
- [ ] Feature Tests للـ stubs المولّدة
- [ ] تحديث GitHub Actions workflow ليشغل الـ tests عند كل push

---

## 📌 ملاحظات

- كل command جديد يجب أن:
  - [ ] يستخدم `CanDisplayLogo` trait
  - [ ] يُسجَّل في `LaunchPointServiceProvider`
  - [ ] يكون له stub خاص به في `src/stubs/`
  - [ ] يُوثَّق في `README.md`
