<?php

namespace YasserElgammal\LogistiTawseel\Support;

class ErrorCodeMapper
{
    private const MESSAGES = [
        0 => ['en' => 'Internal system error, please retry later / or success based on response context', 'ar' => 'خطأ داخلي، الرجاء إعادة المحاولة لاحقًا'],
        2 => ['en' => 'Not found', 'ar' => 'لم يتم العثور على المندوب أو الطلب'],
        5 => ['en' => 'Invalid credentials', 'ar' => 'اسم المستخدم أو كلمة المرور خاطئة'],
        20 => ['en' => 'Invalid ID number', 'ar' => 'رقم الهوية غير صحيح'],
        22 => ['en' => 'City does not belong to region', 'ar' => 'المدينة لا تنتمي للمنطقة'],
        47 => ['en' => 'Driver already exists', 'ar' => 'المندوب مسجل بالفعل'],
        52 => ['en' => 'Order cannot be accepted', 'ar' => 'لا يمكن قبول الطلب لأن حالته ليست جديد'],
        53 => ['en' => 'Order cannot be canceled', 'ar' => 'لا يمكن إلغاء الطلب لأنه ملغي بالفعل أو منفذ أو مرفوض'],
        57 => ['en' => 'Driver must be assigned first', 'ar' => 'يجب أولاً تعيين مندوب على الطلب'],
        58 => ['en' => 'Order number already created today', 'ar' => 'رقم الطلب موجود بالفعل لهذا اليوم'],
        60 => ['en' => 'Invalid mobile number, must start with 05 and contain 10 digits', 'ar' => 'رقم الجوال يجب أن يتكون من 10 خانات ويبدأ بـ 05'],
        72 => ['en' => 'Assigned driver can be done only for accepted order', 'ar' => 'لا يمكن تعيين مندوب على طلب حالته ليست مقبول'],
        80 => ['en' => 'ID number expired', 'ar' => 'بطاقة الهوية أو الإقامة منتهية'],
        84 => ['en' => 'Vehicle information does not match the registered driver', 'ar' => 'بيانات المركبة لا تتطابق مع بيانات السائق المسجل'],
        85 => ['en' => 'Vehicle sequence number required', 'ar' => 'الرقم التسلسلي للمركبة مطلوب'],
        86 => ['en' => 'Invalid vehicle sequence number', 'ar' => 'الرقم التسلسلي للمركبة غير صحيح'],
        90 => ['en' => 'Driving license is expired', 'ar' => 'رخصة القيادة منتهية'],
        95 => ['en' => 'Driver reached maximum orders per day', 'ar' => 'المندوب وصل للحد الأعلى من الطلبات المستلمة لليوم الواحد'],
        97 => ['en' => 'Driver has active order assigned in another app', 'ar' => 'المندوب يقوم بتوصيل طلب في تطبيق آخر حاليًا'],
        98 => ['en' => 'Driver is deactivated by app', 'ar' => 'المندوب معطل من قبل التطبيق'],
        105 => ['en' => 'Incorrect recipient mobile number format', 'ar' => 'رقم جوال المستفيد غير صحيح'],
        106 => ['en' => 'Incorrect price format', 'ar' => 'صيغة سعر الطلب أو سعر التوصيل أو دخل السائق غير صحيحة'],
        107 => ['en' => 'Email required', 'ar' => 'البريد الإلكتروني مطلوب'],
        108 => ['en' => 'Incorrect email format', 'ar' => 'صيغة البريد الإلكتروني غير صحيحة'],
        110 => ['en' => 'There is no contact info for this app', 'ar' => 'لا يوجد بيانات تواصل للتطبيق'],
        112 => ['en' => 'No operation card', 'ar' => 'لا يوجد بطاقة تشغيل للمركبة'],
        113 => ['en' => 'No active operation card', 'ar' => 'بطاقة تشغيل المركبة غير نشطة'],
        114 => ['en' => 'Violated allowed distance', 'ar' => 'المسافة بين المتجر وموقع التوصيل تتجاوز المسافة المسموحة'],
        116 => ['en' => 'Invalid coordinates', 'ar' => 'إحداثيات التوصيل غير صحيحة'],
        117 => ['en' => 'Invalid store location', 'ar' => 'إحداثيات المتجر غير صحيحة'],
        123 => ['en' => 'Driver suspended by TGA', 'ar' => 'السائق موقوف من قبل الهيئة العامة للنقل'],
        124 => ['en' => 'Driver has no vehicle', 'ar' => 'السائق لا يملك مركبة'],
        126 => ['en' => 'Motorcycle permitted at this time', 'ar' => 'لا يسمح باستخدام الدراجات الآلية في التاريخ/الوقت المحدد'],
        132 => ['en' => 'Driver face verification failed', 'ar' => 'فشل التحقق من الوجه للسائق'],
        133 => ['en' => 'No driver card found', 'ar' => 'لا يوجد بطاقة سائق'],
        134 => ['en' => 'No active driver card found', 'ar' => 'بطاقة السائق غير نشطة'],
        135 => ['en' => 'No active vehicle insurance policy', 'ar' => 'لا يوجد وثيقة تأمين سارية على المركبة'],
        136 => ['en' => 'VehicleMVPIIsExpired', 'ar' => 'الفحص الدوري للمركبة منتهي الصلاحية'],
    ];

    public static function message(int|string $code, string $locale = 'en'): ?string
    {
        return self::MESSAGES[(int) $code][$locale] ?? self::MESSAGES[(int) $code]['en'] ?? null;
    }

    public static function messages(array $codes, string $locale = 'en'): array
    {
        return array_values(array_filter(array_map(fn ($code) => self::message($code, $locale), $codes)));
    }

    public static function all(): array
    {
        return self::MESSAGES;
    }
}
