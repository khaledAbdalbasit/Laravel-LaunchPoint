# LaunchPoint — TODO

## 🔴 أولوية عالية

- [x] `launchpoint:make-model` — إنشاء Model مع `$fillable` جاهزة
  - [x] `--migration` — إنشاء migration مع الـ model
  - [x] `--factory` — إنشاء factory
  - [x] `--seeder` — إنشاء seeder
  - [x] `--all` — كل ما سبق دفعة واحدة

- [x] `launchpoint:make-request` — إنشاء FormRequest منظم
  - [x] `authorize()` + `rules()` + `messages()` + `attributes()` جاهزين
  - [x] `failedValidation()` يرجع JSON بدل redirect
  - [x] `--model=` — hint في الـ docblock

- [x] `launchpoint:make-resource` — إنشاء API Resource
  - [x] `--collection` — يولد ResourceCollection معاه

- [x] Pagination في `ApiResponseTrait`
  - [x] دعم `LengthAwarePaginator` تلقائياً
  - [x] إضافة `meta` (current_page, last_page, per_page, total, from, to)
  - [x] إضافة `links` (first, last, prev, next)
  - [x] دعم `errors` key في الـ response

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
  - [x] يستخدم `CanDisplayLogo` trait
  - [x] يُسجَّل في `LaunchPointServiceProvider`
  - [ ] يكون له stub خاص به في `src/stubs/` (للـ commands الجديدة)
  - [ ] يُوثَّق في `README.md`
