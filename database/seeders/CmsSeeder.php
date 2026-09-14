<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. FAQ Categories (20 records)
        $faqCats = [];
        $faqCatNames = [
            'General Booking Inquiries', 'Rental Requirements & Eligibility', 'Pricing, Rates & Discounts',
            'Fleet & Vehicle Types', 'Self-Drive vs Chauffeur Services', 'Insurance & Roadside Safety',
            'Pickup & Drop-off Logistics', 'Cancellations & Refund Policies', 'Owner Fleet Partnerships',
            'Driver Verification & Conduct', 'Payment Gateways & Invoicing', 'Long-term & Corporate Leasing',
            'Airport Transfers & Schedules', 'Cross-Border & Permit Regulations', 'EV Fleet & Charging Network',
            'Vehicle Maintenance Standards', 'GPS & Real-Time Tracking', 'Accidents & Emergency Assistance',
            'Child Seats & Extra Add-ons', 'Mobile App & Account Management',
        ];
        foreach ($faqCatNames as $i => $name) {
            $faqCats[] = [
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => 'Comprehensive guidance and questions regarding '.$name.'.',
                'position' => $i + 1,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('faq_categories')->insert($faqCats);

        // 2. FAQs (20 records)
        $faqs = [];
        $faqQuestions = [
            'What documents are needed to rent a car?' => 'Valid driving license, national ID or passport, and a credit/debit card for deposit.',
            'Can I book a car for self-drive or with a driver?' => 'Both options are available. You can choose self-drive or hire a certified chauffeur.',
            'What happens if the vehicle breaks down during my trip?' => 'We provide 24/7 complimentary roadside assistance and an immediate replacement car.',
            'Is fuel included in the daily rental rate?' => 'Standard rentals are full-to-full fuel policy unless selected as part of an all-inclusive package.',
            'Can I pick up the car at Kathmandu Airport and drop it in Pokhara?' => 'Yes, our inter-city one-way drop facility allows seamless city-to-city transfers.',
            'What is the security deposit policy?' => 'A refundable pre-authorization deposit is placed at vehicle pickup and released upon return inspection.',
            'How early can I cancel a booking for a full refund?' => 'Cancellations made up to 24 hours prior to pickup receive a 100% full refund.',
            'How do car owners list their vehicles on this platform?' => 'Owners register an account, upload vehicle docs and bluebook, and start earning after admin approval.',
            'Are vehicles equipped with GPS and safety kits?' => 'Yes, all vehicles feature real-time GPS tracking, emergency first-aid, and toolkit essentials.',
            'What is the minimum age to rent and drive?' => 'The minimum driving age is 21 years with at least 2 years of active driving license validity.',
            'Are pets allowed in rental vehicles?' => 'Pets are allowed in designated pet-friendly vehicles with prior notice and protective covers.',
            'Can I extend my booking period while on the road?' => 'Yes, you can extend your rental via the web portal or by calling our 24/7 support line.',
            'What insurance coverage is provided with the car?' => 'Standard comprehensive third-party and collision damage waiver (CDW) is included with all rentals.',
            'Do you offer luxury sedans for weddings and VIP events?' => 'Yes, our VIP fleet includes Mercedes, Toyota Prado, Audi, and luxury passenger coaches.',
            'How are toll fees and highway taxes handled?' => 'Tolls and highway permits are payable on-the-go by the renter or included in chauffeur packages.',
            'What payment methods do you accept?' => 'We accept Visa, MasterCard, eSewa, Khalti, Bank Wire transfers, and major mobile wallets.',
            'Can foreign tourists drive with an International Driving Permit (IDP)?' => 'Yes, tourists holding a valid IDP and passport are fully eligible to rent and drive.',
            'How do I view my rental invoices and tax receipts?' => 'All invoices are downloadable in PDF format directly from your customer dashboard.',
            'What safety measures are taken between rentals?' => 'Every vehicle undergoes a 30-point mechanical inspection and complete sanitization before handover.',
            'Is 24/7 customer support available during holidays?' => 'Our dedicated concierge and dispatch support operates 24/7/365 without exception.',
        ];
        $faqIndex = 0;
        foreach ($faqQuestions as $q => $a) {
            $faqs[] = [
                'title' => $q,
                'short_description' => Str::limit($a, 80),
                'description' => '<p>'.$a.'</p>',
                'faq_category_id' => ($faqIndex % 20) + 1,
                'position' => $faqIndex + 1,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $faqIndex++;
        }
        DB::table('faqs')->insert($faqs);

        // 3. Blog Categories (20 records)
        $blogCats = [];
        $blogCatNames = [
            'Travel Guides & Destinations', 'Road Trip Itineraries', 'Vehicle Maintenance Tips',
            'Fleet Management Best Practices', 'Driving Safety & Regulations', 'Electric Vehicles & Sustainability',
            'Car Reviews & Comparisons', 'Luxury Travel Experiences', 'Off-Road Adventure Routes',
            'Rental Tips for Beginners', 'Corporate Mobility Solutions', 'Chauffeur Etiquette & Standards',
            'Seasonal Driving Tips', 'Budget Travel Hacks', 'Nepal Highway Guides',
            'Customer Stories & Testimonials', 'Industry News & Mobility Trends', 'Car Tech & In-Cabin Gadgets',
            'Scenic Mountain Drives', 'Platform Updates & Feature Highlights',
        ];
        foreach ($blogCatNames as $i => $name) {
            $blogCats[] = [
                'title' => $name,
                'slug' => Str::slug($name),
                'description' => 'Articles and in-depth guides covering '.$name.'.',
                'position' => $i + 1,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('blog_categories')->insert($blogCats);

        // 4. Blogs (20 records)
        $blogs = [];
        $blogTitles = [
            'Top 10 Scenic Road Trips You Must Take Across Nepal',
            'Self-Drive vs Chauffeur: Which is Right for Your Next Journey?',
            'Essential Checklist for High-Altitude Mountain Driving in SUVs',
            'How to Maximize Fuel Efficiency on Long Highway Drives',
            'The Rise of Electric Vehicles in the Rental Car Industry',
            'Exploring the Pokhara Circuit: Best Stops, Viewpoints & Routes',
            'Everything You Need to Know About Car Rental Insurance & CDW',
            'How Fleet Owners Earn Steady Passive Income with Our Platform',
            'Ultimate Guide to Monsoon Road Trips: Safety Tips & Gear',
            'Why 4x4 SUVs are the Ideal Choice for Nepal Wilderness Safaris',
            'Navigating Kathmandu City: Parking Tips & Traffic Hacks',
            'The Luxury Experience: Renting Premium Sedans for Special Occasions',
            'How to Prepare Your Family for a Multi-Day Road Trip Adventure',
            'Understanding Bluebook Documentation & Vehicle Fitness Standards',
            'Top 5 Off-The-Beaten-Path Destinations Accessible by Rental Car',
            'Eco-Friendly Driving: Practical Ways to Lower Your Carbon Footprint',
            'A Complete Highway Route Guide: Kathmandu to Pokhara Expressway',
            'How Our 24/7 Roadside Assistance Keeps You Safe Anywhere, Anytime',
            'Choosing the Right Vehicle: Hatchback, Sedan, SUV, or Van?',
            'Behind the Scenes: How We Maintain Pristine Fleet Cleanliness',
        ];
        foreach ($blogTitles as $i => $title) {
            $blogs[] = [
                'title' => $title,
                'slug' => Str::slug($title),
                'author_name' => 'Editorial Fleet Team',
                'publish_date' => $now->copy()->subDays($i * 2),
                'content' => '<h2>'.$title.'</h2><p>Planning your next road excursion requires reliable transportation, pristine safety records, and clear itinerary planning. In this article, our fleet experts explore key insights, safety guidelines, and scenic highlights for an unforgettable journey.</p><p>Whether traveling for leisure, corporate assignments, or family vacations, our modern fleet ensures optimum comfort, reliability, and peace of mind.</p>',
                'category_id' => ($i % 20) + 1,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('blogs')->insert($blogs);

        // 5. Careers (20 records)
        $careers = [];
        $careerTitles = [
            'Senior Fleet Operations Manager', 'Customer Support Concierge Specialist', 'Lead Full Stack PHP/Vue Engineer',
            'Certified VIP Chauffeur / Driver', 'Vehicle Maintenance & Inspection Lead', 'Digital Marketing & Growth Strategist',
            'Financial Analyst & Billing Officer', 'Fleet Dispatch Coordinator', 'Safety & Quality Compliance Auditor',
            'Corporate Sales & Partnerships Lead', 'Mobile App UX/UI Designer', 'Regional Depot Supervisor - Pokhara',
            'Automotive Technician / Mechanic', 'Social Media & Content Creator', 'Legal & Regulatory Compliance Officer',
            'Data Analyst - Telematics & Fleet AI', 'Night Dispatch & Emergency Officer', 'Talent Acquisition & HR Officer',
            'Procurement & Inventory Specialist', 'Customer Success Associate',
        ];
        foreach ($careerTitles as $i => $title) {
            $careers[] = [
                'title' => $title,
                'slug' => Str::slug($title),
                'employment_type' => ($i % 2 == 0) ? 'Full Time' : 'Contract',
                'job_level' => ($i < 5) ? 'Senior Level' : 'Mid Level',
                'job_location' => ($i % 4 == 0) ? 'Pokhara Hub' : 'Kathmandu Headquarters',
                'no_of_vacancies' => ($i % 3) + 1,
                'offered_salary' => 'Competitive + Performance Bonuses',
                'apply_before' => $now->copy()->addDays(30 + $i)->format('Y-m-d'),
                'description' => '<p>We are seeking a talented '.$title.' to join our fast-growing mobility and fleet enterprise. Enjoy dynamic team culture, comprehensive healthcare, and clear career growth.</p>',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('careers')->insert($careers);

        // 6. Career Applications (20 records)
        $apps = [];
        for ($i = 0; $i < 20; $i++) {
            $apps[] = [
                'career_id' => ($i % 20) + 1,
                'first_name' => 'Candidate',
                'last_name' => 'Applicant #'.($i + 1),
                'email' => 'candidate'.($i + 1).'@example.com',
                'phone' => '+977-9841'.str_pad($i + 100, 6, '0', STR_PAD_LEFT),
                'received_at' => $now->copy()->subDays($i)->format('Y-m-d H:i:s'),
                'is_read' => ($i % 2 == 0) ? '1' : '0',
                'is_shortlisted' => ($i % 3 == 0) ? '1' : '0',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('career_applications')->insert($apps);

        // 7. Notices (20 records)
        $notices = [];
        $noticeTitles = [
            'Notice on Monsoon Highway Driving Safety Protocols',
            'Scheduled Server & API Maintenance Window (Midnight - 2 AM)',
            'Special Festive Season Booking Discounts Now Open',
            'Updated Vehicle Sanitization & Hygiene Standards',
            'New EV Charging Hub Opened at Kathmandu Central',
            'Expansion of Pokhara Airport Concierge Services',
            'Winter Tire & Fog Driving Recommendations',
            'Driver Professional Certification Workshop 2026',
            'Annual Fleet Safety Inspection Schedule Announcement',
            'Notice Regarding Fuel Surcharge Updates',
            'New VIP Chauffeur Fleet Added to Platform',
            'Updated Terms & Cancellation Policy Notice',
            'Express Online Self-Check-in Now Live',
            'Holiday Operating Hours & Customer Hotline',
            'Owner Earnings Disbursement Cycle Update',
            'Road Alert: Highway Widening Near Mugling - Expect Minor Delays',
            'Launch of 24/7 Multi-lingual Concierge Assistance',
            'Safety Recall & Routine Brake Inspection Campaign',
            'New Corporate Billing & Invoicing Features Released',
            'Customer Appreciation Month: 15% Bonus Rental Credits',
        ];
        foreach ($noticeTitles as $i => $title) {
            $notices[] = [
                'title' => $title,
                'slug' => Str::slug($title),
                'start_date' => $now->copy()->subDays($i)->format('Y-m-d'),
                'end_date' => $now->copy()->addDays(30 + $i)->format('Y-m-d'),
                'description' => '<p>'.$title.' - All customers, fleet owners, and certified drivers are requested to take note of the operational updates.</p>',
                'position' => $i + 1,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('notices')->insert($notices);

        // 8. Teams (20 records)
        $teams = [];
        $teamMembers = [
            ['name' => 'Pashupati Sah', 'role' => 'Founder & Chief Executive Officer', 'email' => 'pashupati@carrental.com'],
            ['name' => 'Aarav Sharma', 'role' => 'Chief Operating Officer', 'email' => 'aarav.ops@carrental.com'],
            ['name' => 'Sunita Shrestha', 'role' => 'Head of Customer Experience', 'email' => 'sunita.cx@carrental.com'],
            ['name' => 'Bikash Adhikari', 'role' => 'Chief Financial Officer', 'email' => 'bikash.fin@carrental.com'],
            ['name' => 'Puja Karki', 'role' => 'VP of Marketing & Brand', 'email' => 'puja.mkt@carrental.com'],
            ['name' => 'Rohan Gurung', 'role' => 'Regional Director - Pokhara', 'email' => 'rohan.west@carrental.com'],
            ['name' => 'Manish Thapa', 'role' => 'Head of Fleet Safety & Compliance', 'email' => 'manish.safety@carrental.com'],
            ['name' => 'Anjali Joshi', 'role' => 'Director of Human Resources', 'email' => 'anjali.hr@carrental.com'],
            ['name' => 'Deepak KC', 'role' => 'Principal Software Architect', 'email' => 'deepak.tech@carrental.com'],
            ['name' => 'Nisha Tamang', 'role' => 'Senior Financial Controller', 'email' => 'nisha.acc@carrental.com'],
            ['name' => 'Sanjay Mahato', 'role' => 'QA & Technical Operations Lead', 'email' => 'sanjay.qa@carrental.com'],
            ['name' => 'Rashmi Poudel', 'role' => 'Reservations & Booking Manager', 'email' => 'rashmi.book@carrental.com'],
            ['name' => 'Bibek Dahal', 'role' => 'Logistics & Depot Supervisor', 'email' => 'bibek.depot@carrental.com'],
            ['name' => 'Sita Gautam', 'role' => 'Client Care Executive Lead', 'email' => 'sita.care@carrental.com'],
            ['name' => 'Kiran Basnet', 'role' => 'Telematics & Security Lead', 'email' => 'kiran.sec@carrental.com'],
            ['name' => 'Karuna Rai', 'role' => 'Partner Relations Manager', 'email' => 'karuna.partner@carrental.com'],
            ['name' => 'Prakash Yadav', 'role' => 'Master Fleet Mechanic Supervisor', 'email' => 'prakash.mech@carrental.com'],
            ['name' => 'Samir Bista', 'role' => 'Legal Counsel & Regulatory Head', 'email' => 'samir.legal@carrental.com'],
            ['name' => 'Maya Dangol', 'role' => 'Lead UI/UX Designer', 'email' => 'maya.design@carrental.com'],
            ['name' => 'Rajendra Giri', 'role' => 'Chauffeur Training Director', 'email' => 'rajendra.train@carrental.com'],
        ];
        foreach ($teamMembers as $i => $m) {
            $teams[] = [
                'name' => $m['name'],
                'slug' => Str::slug($m['name']),
                'designation' => $m['role'],
                'email' => $m['email'],
                'phone' => '+977-9841'.str_pad($i + 500, 6, '0', STR_PAD_LEFT),
                'description' => $m['name'].' brings over 10 years of automotive and mobility leadership experience.',
                'position' => $i + 1,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('teams')->insert($teams);

        // 9. Testimonials (20 records)
        $testimonials = [];
        $clientReviews = [
            ['name' => 'Dr. Sameer Shrestha', 'title' => 'Exceptional Service for Family Vacations', 'rating' => 5.0, 'job' => 'Senior Surgeon, KMC'],
            ['name' => 'Elena Rostova', 'title' => 'Flawless 4x4 Fortuner for Annapurna Trek', 'rating' => 5.0, 'job' => 'International Travel Journalist'],
            ['name' => 'Rajiv Agrawal', 'title' => 'Prompt Airport Handover & Spotless Car', 'rating' => 5.0, 'job' => 'Managing Director, Agrawal Group'],
            ['name' => 'Sarah Jenkins', 'title' => 'The Chauffeur was Polite and Knowledgeable', 'rating' => 4.8, 'job' => 'Documentary Filmmaker, UK'],
            ['name' => 'Bishnu Prasad Pokhrel', 'title' => 'Transparent Rates with Zero Hidden Fees', 'rating' => 5.0, 'job' => 'Chartered Accountant'],
            ['name' => 'Anil Khadka', 'title' => 'Best Corporate Leasing Fleet in Nepal', 'rating' => 4.9, 'job' => 'Operations Lead, TechCorp'],
            ['name' => 'Meera Gurung', 'title' => 'Super Smooth Booking on Mobile Portal', 'rating' => 5.0, 'job' => 'Hospitality Consultant, Pokhara'],
            ['name' => 'Thomas Weber', 'title' => 'Reliable Mountain Vehicle with Top Tires', 'rating' => 5.0, 'job' => 'Expedition Leader, Germany'],
            ['name' => 'Nabin Maharjan', 'title' => 'Great Support During Our Chitwan Safari Tour', 'rating' => 4.7, 'job' => 'Architect, Lalitpur'],
            ['name' => 'Sunil Pradhan', 'title' => 'Listing My Car as an Owner Was Very Profitable', 'rating' => 5.0, 'job' => 'Fleet Vehicle Investor'],
            ['name' => 'Priya Gautam', 'title' => 'Luxury Mercedes for Our Wedding Day was Perfect', 'rating' => 5.0, 'job' => 'Event Planner'],
            ['name' => 'David Kim', 'title' => 'Clean EV with Rapid Charging Guidance', 'rating' => 4.9, 'job' => 'Eco-Tourism Researcher, Korea'],
            ['name' => 'Santosh Thapa', 'title' => 'Fast 24/7 Roadside Assistance Experience', 'rating' => 5.0, 'job' => 'Software Engineer'],
            ['name' => 'Alisha Shrestha', 'title' => 'Highly Recommend the Weekly Rental Discount', 'rating' => 4.8, 'job' => 'Brand Strategist'],
            ['name' => 'Dipak Lamichhane', 'title' => 'Professional Drivers with Clean Background Checks', 'rating' => 5.0, 'job' => 'School Principal'],
            ['name' => 'John Bradley', 'title' => 'First Class Hospitality from Start to Finish', 'rating' => 5.0, 'job' => 'Photographer, Australia'],
            ['name' => 'Kabita Basnet', 'title' => 'Child Seat and Emergency First-Aid Included', 'rating' => 4.9, 'job' => 'Medical Researcher'],
            ['name' => 'Suman Koirala', 'title' => 'Quick Return Inspection and Deposit Release', 'rating' => 5.0, 'job' => 'Civil Engineer'],
            ['name' => 'Rhea Sharma', 'title' => 'Impeccable Interiors and Great AC Performance', 'rating' => 4.8, 'job' => 'Creative Director'],
            ['name' => 'Manoj Tamang', 'title' => 'Our Go-To Rental Service for Every Road Trip', 'rating' => 5.0, 'job' => 'Tourism Entrepreneur'],
        ];
        foreach ($clientReviews as $i => $r) {
            $testimonials[] = [
                'name' => $r['name'],
                'title' => $r['title'],
                'job_title' => $r['job'],
                'rating' => $r['rating'],
                'description' => '"'.$r['title'].'. '.$r['name'].' rated our fleet service '.$r['rating'].' stars for exemplary vehicle maintenance, prompt customer service, and smooth logistics."',
                'position' => $i + 1,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('testimonials')->insert($testimonials);

        // 10. Services (20 records)
        $services = [];
        $serviceList = [
            ['title' => 'Self-Drive Car Rentals', 'price' => 75.00],
            ['title' => 'Chauffeur-Driven Luxury Sedans', 'price' => 120.00],
            ['title' => 'Airport Express Pickup & Drop', 'price' => 45.00],
            ['title' => 'Off-Road 4x4 Mountain Expedition', 'price' => 160.00],
            ['title' => 'Inter-City One-Way Drop Rentals', 'price' => 110.00],
            ['title' => 'VIP & Diplomatic Motorcade Escorts', 'price' => 350.00],
            ['title' => 'Wedding & Bridal Luxury Cars', 'price' => 250.00],
            ['title' => 'Long-Term Corporate Fleet Leasing', 'price' => 950.00],
            ['title' => 'Electric Vehicle (EV) Eco-Rentals', 'price' => 85.00],
            ['title' => 'Chitwan Safari SUV Packages', 'price' => 220.00],
            ['title' => 'Pokhara Weekend Tour Fleet', 'price' => 180.00],
            ['title' => 'Sightseeing & Heritage City Tours', 'price' => 90.00],
            ['title' => 'Multi-Passenger Van & Minibus Hire', 'price' => 200.00],
            ['title' => 'Emergency 24/7 Roadside Rescue & Tow', 'price' => 60.00],
            ['title' => 'Customized Filming & Crew Transport', 'price' => 280.00],
            ['title' => 'Trekking Trailhead Transfer Services', 'price' => 140.00],
            ['title' => 'Luxury Camper & Road Trip Vans', 'price' => 230.00],
            ['title' => 'Cross-District Logistics Vehicles', 'price' => 130.00],
            ['title' => 'GPS & Safety Add-on Equipments', 'price' => 25.00],
            ['title' => 'Fleet Owner Management & Listing', 'price' => 0.00],
        ];
        foreach ($serviceList as $i => $s) {
            $services[] = [
                'title' => $s['title'],
                'slug' => Str::slug($s['title']),
                'price' => $s['price'],
                'description' => '<p>Professional '.$s['title'].' offering unmatched reliability, full comprehensive insurance, and 24/7 dispatch support.</p>',
                'position' => $i + 1,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('services')->insert($services);

        // 11. Slider Types & Sliders (20 records each)
        $sliderTypes = [];
        for ($i = 1; $i <= 20; $i++) {
            $sliderTypes[] = [
                'title' => 'Slider Section Category #'.$i,
                'slug' => 'slider-type-'.$i,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('slider_types')->insert($sliderTypes);

        $sliders = [];
        $sliderHeadings = [
            'Premium Car Rentals for Every Adventure',
            'Explore Nepal with 4x4 Off-Road Power',
            'Travel in Luxury with Certified Chauffeurs',
            'Instant Online Booking with Zero Hidden Fees',
            'Airport Pickup & Drop-off in 15 Minutes',
            'Experience Clean, Modern Electric Fleet',
            'Unmatched Comfort on Long Highway Drives',
            'Corporate Fleet Leasing Tailored for You',
            'Discover Pokhara Lakes with Luxury Sedans',
            'Chitwan Wildlife Safaris in Rugged SUVs',
            '24/7 Roadside Assistance Wherever You Travel',
            'Join as a Fleet Owner & Maximize Returns',
            'Exclusive Weekend Getaway Special Rates',
            'Family Vacation Vans & Comfortable Coaches',
            'Safety First: 30-Point Vehicle Check Guarantee',
            'Himalayan Road Trips with Trusted Drivers',
            'Seamless Inter-City One-Way Transfers',
            'VIP Wedding & Event Motorcade Packages',
            'Download Our App for Instant Road Access',
            'Your Journey, Your Choice — Drive Premium',
        ];
        foreach ($sliderHeadings as $i => $h) {
            $sliders[] = [
                'title' => $h,
                'slug' => Str::slug($h),
                'heading_text' => $h,
                'sub_heading_text' => 'Book verified luxury cars, SUVs, and certified drivers in seconds.',
                'button_text' => 'Book Now',
                'link' => '/cars',
                'slider_type_id' => ($i % 20) + 1,
                'position' => $i + 1,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('sliders')->insert($sliders);

        // 12. Partners (20 records)
        $partners = [];
        $partnerNames = [
            'Nepal Tourism Board', 'Toyota Nepal (United Traders)', 'Hyundai Motoring Corp',
            'Himalayan Bank Ltd.', 'Nabil Bank Digital Pay', 'eSewa Digital Wallet',
            'Khalti Payment Solutions', 'Radisson Hotel Group', 'Marriott International Kathmandu',
            'Annapurna Trekking Association', 'Pokhara Tourism Council', 'Chitwan Eco-Lodge Alliance',
            'Kathmandu Marriott VIP Lounge', 'Patan Heritage Council', 'Nepal Road Safety Alliance',
            'Everest Air Logistics', 'Lumbini Heritage Trust', 'Nepal Automobile Association (NASA)',
            'BYD Electric Mobility Nepal', 'Castrol Lubricants Official',
        ];
        foreach ($partnerNames as $i => $p) {
            $partners[] = [
                'title' => $p,
                'slug' => Str::slug($p),
                'url' => 'https://example.com/'.Str::slug($p),
                'description' => 'Official strategic alliance partner: '.$p,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('partners')->insert($partners);

        // 13. Popups (20 records)
        $popups = [];
        for ($i = 1; $i <= 20; $i++) {
            $popups[] = [
                'title' => 'Promotional Popup Campaign #'.$i.': Festive 20% Discount',
                'slug' => 'popup-promo-'.$i,
                'type' => 'promotional',
                'description' => '<p>Use promo code <strong>FESTIVE'.$i.'</strong> to claim special rental discounts on all SUV bookings this week!</p>',
                'link' => '/cars',
                'position' => $i,
                'is_active' => 1,
                'start_date' => $now->copy()->subDays(5),
                'end_date' => $now->copy()->addDays(45),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('popups')->insert($popups);

        // 14. Pages (20 records)
        $pages = [];
        $pageTitles = [
            'About Our Fleet Enterprise', 'Terms & Rental Agreement Conditions', 'Privacy & Data Protection Policy',
            'Vehicle Insurance & Collision Damage Waiver', 'Fleet Safety & Cleanliness Standards', 'Chauffeur Code of Conduct',
            'How It Works: Step-by-Step Guide', 'Owner Partnership Program Details', 'Corporate Account Benefits',
            'Cancellation & Refund Regulations', 'Highway Toll & Permit Guide', 'Electric Vehicle Charging Network',
            'Roadside Emergency & Towing Procedures', 'Airport Meet & Greet Service Guide', 'Customer Loyalty & Reward Points',
            'Driving in Nepal: International Tourist Guide', 'Sustainability & Green Travel Commitment', 'Careers & Work Culture',
            'Press Releases & Media Kit', 'Contact Us & Depot Locations',
        ];
        foreach ($pageTitles as $i => $title) {
            $pages[] = [
                'title' => $title,
                'slug' => Str::slug($title),
                'custom_slug' => Str::slug($title),
                'content' => '<h1>'.$title.'</h1><p>Welcome to our comprehensive information portal regarding '.$title.'. Our platform is built on principles of safety, transparency, premium hospitality, and technological innovation in vehicle mobility.</p>',
                'position' => $i + 1,
                'views' => 100 * ($i + 1),
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('pages')->insert($pages);

        // 15. News & Updates (20 records)
        $news = [];
        for ($i = 1; $i <= 20; $i++) {
            $news[] = [
                'title' => 'Latest Industry News #'.$i.': Fleet System Expands Across New Provinces',
                'slug' => 'news-update-'.$i,
                'url' => 'https://news.carrental.com/update-'.$i,
                'published_by' => 'Corporate Communications Desk',
                'publish_date' => $now->copy()->subDays($i),
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('news_and_updates')->insert($news);

        // 16. Menus & Menu Items (20 records each)
        $menus = [];
        for ($i = 1; $i <= 20; $i++) {
            $menus[] = [
                'title' => 'Navigation Menu Set #'.$i,
                'slug' => 'nav-menu-'.$i,
                'location' => ($i == 1) ? 'header' : (($i == 2) ? 'footer' : 'sidebar_'.$i),
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('menus')->insert($menus);

        $menuItems = [];
        $menuItemLabels = [
            'Home', 'Explore Fleet', 'Luxury SUVs', 'Economy Sedans', 'Electric Cars (EV)',
            'Chauffeur Services', 'About Us', 'Services', 'Pricing & Rates', 'Travel Blog',
            'Driver Directory', 'Owner Partnership', 'Careers', 'Notices', 'FAQs',
            'Customer Reviews', 'Photo Albums', 'Terms & Conditions', 'Help Center', 'Contact Us',
        ];
        foreach ($menuItemLabels as $i => $label) {
            $menuItems[] = [
                'title' => $label,
                'slug' => Str::slug($label),
                'type' => 'custom',
                'value' => '/'.Str::slug($label),
                'menu_id' => ($i % 20) + 1,
                'position' => $i + 1,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('menu_items')->insert($menuItems);

        // 17. Albums & Album Values (20 records each)
        $albums = [];
        for ($i = 1; $i <= 20; $i++) {
            $albums[] = [
                'title' => 'Fleet Showcase Album #'.$i.': Scenic Himalayan Expeditions',
                'slug' => 'album-'.$i,
                'description' => 'Visual gallery documenting our luxury vehicles on mountain terrains.',
                'event_date' => $now->copy()->subDays($i * 5)->format('Y-m-d'),
                'position' => $i,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('albums')->insert($albums);

        $albumValues = [];
        for ($i = 1; $i <= 20; $i++) {
            $albumValues[] = [
                'album_id' => ($i % 20) + 1,
                'title' => 'High-Resolution Vehicle Photo #'.$i,
                'slug' => 'photo-'.$i,
                'path' => 'albums/gallery_'.$i.'.jpg',
                'is_featured' => ($i % 2 == 0) ? 1 : 0,
                'position' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('album_values')->insert($albumValues);

        // 18. Enquiries (20 records)
        $enquiries = [];
        for ($i = 1; $i <= 20; $i++) {
            $enquiries[] = [
                'name' => 'Prospective Client #'.$i,
                'slug' => 'enquiry-client-'.$i,
                'email' => 'client'.$i.'@inquiry.com',
                'phone' => '+977-9801'.str_pad($i + 200, 6, '0', STR_PAD_LEFT),
                'subject' => 'Inquiry Regarding SUV Booking #'.(1000 + $i),
                'message' => 'Hello, I would like to inquire about availability and corporate discount rates for a 5-day rental.',
                'token' => Str::random(16),
                'mark_as_read' => ($i % 2 == 0) ? 1 : 0,
                'created_at' => $now->copy()->subHours($i * 3),
                'updated_at' => $now->copy()->subHours($i * 3),
            ];
        }
        DB::table('enquiries')->insert($enquiries);

        // 19. Contact Us (20 records)
        $contacts = [];
        for ($i = 1; $i <= 20; $i++) {
            $contacts[] = [
                'name' => 'Inquirer Contact #'.$i,
                'email' => 'contact'.$i.'@visitor.com',
                'phone' => '+977-9811'.str_pad($i + 300, 6, '0', STR_PAD_LEFT),
                'subject' => 'Feedback on Rental Experience #'.(2000 + $i),
                'message' => 'Thank you for providing exceptional customer service and vehicle condition on our recent trip.',
                'is_read' => ($i % 2 == 0) ? 1 : 0,
                'created_at' => $now->copy()->subHours($i * 4),
                'updated_at' => $now->copy()->subHours($i * 4),
            ];
        }
        DB::table('contact_us')->insert($contacts);

        // 20. Site Settings (20 records)
        $settings = [];
        for ($i = 1; $i <= 20; $i++) {
            $settings[] = [
                'company_name' => ($i == 1) ? 'Fleet Master Car Rental & Mobility System' : 'Fleet Depot Branch #'.$i,
                'description' => 'Premier car rental and fleet management platform connecting verified owners, certified drivers, and travelers worldwide.',
                'mobile' => '+977-9841234567',
                'phone' => '+977-1-4455667',
                'email' => 'support@carrental.com',
                'address' => 'Kathmandu, Bagmati Province, Nepal',
                'map_url' => 'https://maps.google.com/?q=Kathmandu',
                'facebook' => 'https://facebook.com/carrental',
                'twitter' => 'https://twitter.com/carrental',
                'instagram' => 'https://instagram.com/carrental',
                'linkedin' => 'https://linkedin.com/company/carrental',
                'youtube' => 'https://youtube.com/@carrental',
                'slogan' => 'Your Journey, Your Freedom',
                'tagline' => 'Luxury Fleet, Certified Drivers & Instant Booking',
                'website' => 'https://carrental.com',
                'copy_right_text' => '© '.date('Y').' Car Rental System. All Rights Reserved.',
                'seo_title' => 'Car Rental System - Premier Car & SUV Hire in Nepal',
                'seo_keyword' => 'car rental, rent car kathmandu, hire driver nepal, 4x4 suv hire pokhara',
                'seo_description' => 'Book luxury sedans, 4x4 SUVs, and certified drivers online with 24/7 assistance and best rates.',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('site_settings')->insert($settings);
    }
}
