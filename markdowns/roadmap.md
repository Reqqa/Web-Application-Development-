# CourseMap — Development Roadmap

A suggested week-by-week plan for a 4-person group, assuming this assignment runs across several weeks leading to the Week 12 due date.

## Phase 1 — Setup & Static Foundation (Week 1–2)
- Set up local server environment (XAMPP/WAMP) and shared Git repository
- Design wireframes for Home, Listing, Details, Contact pages
- Build static HTML/CSS for: Home Page, Navigation Menu, Contact Page
- Establish global stylesheet (`style.css`) and color/typography system
- Deliverable: static, non-functional pages with consistent layout

## Phase 2 — Responsive Layer & Client-side Interactivity (Week 3–4)
- Add `responsive.css` with media queries for mobile/tablet/desktop breakpoints
- Build navigation dropdown behavior with vanilla JS (`main.js`)
- Implement client-side form validation for contact form, login, register (`validation.js`)
- Deliverable: fully responsive static site with working client-side scripts

## Phase 3 — Database & Server-side Foundation (Week 5–6)
- Finalize ER diagram and create `coursemap.sql`
- Set up `includes/db-connect.php`
- Build registration and login system with PHP sessions and password hashing
- Implement `includes/auth-check.php` for route protection (admin-only / student-only pages)
- Deliverable: working authentication system connected to MySQL

## Phase 4 — Core CRUD: Course Management (Week 7–8)
- Admin: course-add.php (Create), courses-manage.php (Read), course-edit.php (Update), course-delete.php (Delete)
- Build courses/listing.php to pull live data from the database
- Build courses/details.php with dynamic course_id lookup
- Deliverable: full admin CRUD cycle functioning against MySQL

## Phase 5 — Student Features (Week 9)
- Build student/my-courses.php (enroll/save/remove courses — the cart/wishlist equivalent)
- Build mark-as-complete functionality (`progress.js` + `student/mark-complete.php`, AJAX-based)
- Build student dashboard showing enrolled courses and completion %
- Deliverable: students can browse, save, enroll, and track progress end-to-end

## Phase 6 — Polish & Secondary Features (Week 10)
- Add reviews/comments on course details page
- Add category filtering/search on listing page
- Refine UI consistency and error handling (e.g. empty states, validation messages)
- Cross-browser and cross-device testing

## Phase 7 — Documentation & Submission Prep (Week 11)
- Write README, finalize SQL seed data
- Prepare project report (cover page, structure, code snippets with explanations, workload summary, references)
- Record 15-minute demo video with each member presenting their portion
- Internal code review — every member must be able to explain all submitted code

## Phase 8 — Final QA & Submission (Week 12)
- Final bug fixes
- Package ZIP per naming convention: `UECS2194_Assignment_PracticalClass_GroupNumber.zip`
- Submit project files, SQL file, README, report PDF, and demo video

## Suggested Role Split (4 members)
1. **Auth & Admin CRUD** — login/register, session handling, admin course management
2. **Student Features** — listing, details, enrollment/wishlist, mark-as-complete
3. **Front-end/UI & Responsiveness** — layout, navigation, CSS, media queries, JS validation
4. **Database & Documentation** — schema design, seed data, README, report compilation

(Adjust based on actual group skillsets — pairing is fine as long as each member can explain the full pipeline during the demo.)
