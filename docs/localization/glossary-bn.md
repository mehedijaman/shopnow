# Bangla Glossary (English → বাংলা)

This glossary is the single source of truth for Bangla terminology. **Create/update it before writing any batch of translations, and apply it everywhere.** This is the main lever for consistency across hundreds of strings written in separate batches.

## Register

- Use the respectful **আপনি** (not তুমি) consistently for address/CTA text.
- Prefer natural, everyday Bangla as used by Bangladeshi consumer apps and e-commerce sites.
- **Loanwords that people actually use are correct**: অর্ডার, কার্ট, চেকআউট, লগইন, সাইন ইন, ডাউনলোড, সার্চ, ব্লগ, ব্র্যান্ড, ক্যাটাগরি, ট্যাগ, প্রোডাক্ট, পেমেন্ট, ডিলিভারি, ডিসকাউন্ট, ট্যাক্স, সালে, স্টক, সেভ. Stilted "pure" replacements (e.g. প্রবেশ instead of লগইন) are **wrong** for this audience.
- Never leave a placeholder out. Always keep `:name`, `:count`, `:attribute` intact.

## Global navigation & chrome

| English | বাংলা | Notes |
|---|---|---|
| Home | হোম | nav |
| Shop | শপ | nav |
| Blog | ব্লগ | nav |
| About | আমাদের সম্পর্কে | nav; long — check mobile |
| Contact | যোগাযোগ | nav |
| Track | ট্র্যাক | nav ("Track Parcel") |
| Track Parcel | পার্সেল ট্র্যাক করুন | mobile menu |
| Search | সার্চ | |
| Menu | মেনু | aria-labels |
| Close | বন্ধ করুন | |
| Open Menu | মেনু খুলুন | |
| Language | ভাষা | switcher |
| Follow Us | আমাদের অনুসরণ করুন | footer |

## Accounts & auth

| English | বাংলা | Notes |
|---|---|---|
| Sign In / Login | লগইন | |
| Sign In / Register | লগইন / রেজিস্টার | header CTA |
| Sign Up / Register | রেজিস্টার | |
| Logout | লগআউট | |
| Sign Out | সাইন আউট | admin |
| My Account | আমার অ্যাকাউন্ট | |
| My Profile | আমার প্রোফাইল | |
| Edit Profile | প্রোফাইল সম্পাদনা | admin topbar |
| My Orders | আমার অর্ডার | |
| My Downloads | আমার ডাউনলোড | |
| Signed in as | সাইন ইন করা আছেন | header dropdown |
| Email | ইমেইল | |
| Password | পাসওয়ার্ড | |
| Password Confirmation | পাসওয়ার্ড নিশ্চিত করুন | |
| Remember me | আমাকে মনে রাখুন | |
| Forgot your password? | পাসওয়ার্ড ভুলে গেছেন? | |
| Reset Password | পাসওয়ার্ড রিসেট করুন | |
| New Password | নতুন পাসওয়ার্ড | |
| Guest | গেস্ট | checkout |

## Orders, cart & checkout

| English | বাংলা | Notes |
|---|---|---|
| Cart | কার্ট | |
| Checkout | চেকআউট | |
| Order | অর্ডার | |
| My Orders / Orders | অর্ডার | |
| Add to Cart | কার্টে যোগ করুন | |
| Buy Now | এখনই কিনুন | |
| Empty Cart | কার্ট খালি | |
| Subtotal | সাবটোটাল | |
| Total | মোট | "টোটাল" also OK; pick মোট |
| Shipping | ডেলিভারি চার্জ | |
| Tax | ট্যাক্স | loanword |
| Discount | ছাড় | |
| Promo Code | প্রোমো কোড | |
| Coupon | কুপন | |
| Quantity | পরিমাণ | |
| Unit Price | একক দাম | |
| Price | দাম | consumer-facing; মূল্য for formal |
| Payment | পেমেন্ট | |
| Payment Method | পেমেন্ট পদ্ধতি | |
| Cash on Delivery | ক্যাশ অন ডেলিভারি | COD; keep loanword |
| Place Order | অর্ডার করুন | |
| Order Confirmation | অর্ডার নিশ্চিতকরণ | |
| Invoice | ইনভয়েস | |
| Refund | রিফান্ড / ফেরত | flag for legal review |

## Statuses (display labels only — backing values stay English)

| English | বাংলা | Notes |
|---|---|---|
| Status | স্ট্যাটাস | |
| Pending | অপেক্ষমাণ | |
| Processing | প্রক্রিয়াধীন | |
| Shipped | পাঠানো হয়েছে | |
| Delivered | ডেলিভার হয়েছে | |
| Completed | সম্পন্ন | |
| Cancelled | বাতিল | |
| Paid | পেমেন্ট হয়েছে | |
| Unpaid | অপরিশোধিত | |
| Success / Succeeded | সফল | |
| Failed | ব্যর্থ | |
| Active | সক্রিয় | |
| Inactive | নিষ্ক্রিয় | |
| In Stock | স্টকে আছে | |
| Out of Stock | স্টকে নেই | |
| Enabled / Disabled | চালু / বন্ধ | settings toggles |
| Yes / No | হ্যাঁ / না | |

## Products & catalog

| English | বাংলা | Notes |
|---|---|---|
| Product | পণ্য | consumer-facing; প্রোডাক্ট OK too |
| Products | পণ্যসমূহ | |
| Category | ক্যাটাগরি | **not** বিভাগ (= Division) |
| Brand | ব্র্যান্ড | |
| Tag | ট্যাগ | |
| Attribute | অ্যাট্রিবিউট | |
| Variation | ভ্যারিয়েশন | |
| Bundle | বান্ডেল | |
| Simple Product | সিম্পল প্রোডাক্ট | |
| Variable Product | ভ্যারিয়েবল প্রোডাক্ট | |
| Bundle Product | বান্ডেল প্রোডাক্ট | |
| Featured | ফিচার্ড | |
| New | নতুন | |
| Sale | সেল | |
| Download | ডাউনলোড | |
| Upload | আপলোড | |
| Download Ready / Expires | ডাউনলোড প্রস্তুত / মেয়াদ | email |

## Address (BD administrative)

| English | বাংলা | Notes |
|---|---|---|
| Division | বিভাগ | geodata `bn_name` |
| District | জেলা | geodata `bn_name` |
| Upazila | উপজেলা | geodata `bn_name` |
| Union | ইউনিয়ন | geodata `bn_name` |
| Address | ঠিকানা | |
| Phone | ফোন | |
| Name | নাম | |

## Admin chrome

| English | বাংলা | Notes |
|---|---|---|
| Dashboard | ড্যাশবোর্ড | |
| Settings | সেটিংস | |
| Users | ব্যবহারকারী | |
| Permissions | অনুমতি | |
| Roles | ভূমিকা | |
| Access Control List | অ্যাক্সেস কন্ট্রোল লিস্ট | |
| Customer Management | কাস্টমার ম্যানেজমেন্ট | |
| Order Management | অর্ডার ম্যানেজমেন্ট | |
| Product Management | পণ্য ম্যানেজমেন্ট | |
| Contact Messages | যোগাযোগ বার্তা | |
| Sliders | স্লাইডার | |
| Pages | পেজ | |
| My Profile | আমার প্রোফাইল | |
| Order Report | অর্ডার রিপোর্ট | |
| Create Order | অর্ডার তৈরি করুন | |
| New Product | নতুন পণ্য | |

## Common actions

| English | বাংলা | Notes |
|---|---|---|
| Save | সেভ | loanword, universal in BD apps |
| Create | তৈরি করুন | |
| Edit | সম্পাদনা | |
| Update | আপডেট | |
| Delete | ডিলিট | |
| Cancel | বাতিল | button |
| Submit | জমা দিন | |
| Send | পাঠান | |
| Continue | চালিয়ে যান | |
| Back | ফিরে যান | |
| Confirm | নিশ্চিত করুন | |
| Are you sure? | আপনি কি নিশ্চিত? | |
| View | দেখুন | |
| Apply | প্রয়োগ করুন | |
| Filter | ফিল্টার | |
| Clear Search | সার্চ মুছুন | |
| No results | কোনো ফলাফল নেই | |
| Loading | লোড হচ্ছে | |
| Required | প্রয়োজনীয় | |

## Pagination

| English | বাংলা | Notes |
|---|---|---|
| Previous | আগের | |
| Next | পরের | |
| Showing | দেখাচ্ছে | |
| to | থেকে | |
| of | এর মধ্যে | |
| results | ফলাফল | |

## Error & validation messages

| English | বাংলা | Notes |
|---|---|---|
| Whoops! Something went wrong... | উফ! কিছু একটা সমস্যা হয়েছে... | |
| Session expired, please login again. | সেশনের মেয়াদ শেষ, অনুগ্রহ করে আবার লগইন করুন। | |
| The given data was invalid. | দেওয়া তথ্যটি অবৈধ। | |
| These credentials do not match our records. | এই তথ্য আমাদের রেকর্ডের সাথে মিলে না। | |
| (validation `required`) | এই ঘরটি পূরণ করা আবশ্যক। | |
| (validation `email`) | এই ঘরটিতে একটি সঠিক ইমেইল ঠিকানা দিন। | |

## Terms NOT translated (identifiers / brand names)

Permission names, route names, enum backing values, status codes, event names (incl. GA events), config keys, DB values, CSS classes, courier provider names (`Pathao`, `Steadfast`, `RedX`, `eCourier`, `Paperfly`), social network names (`Facebook`, `X`, `Instagram`, `YouTube`, `LinkedIn`, `TikTok`, `GitHub`, `WhatsApp`), currency symbol `৳`, brand name `ShopNow`.
