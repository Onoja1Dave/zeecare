CareLink is a Laravel-based web platform designed to improve post-discharge communication between doctors, patients, and caregivers. It ensures patients continue their treatment plans correctly, while also making room for patients who need extra support — especially the elderly or those unfamiliar with digital technology.

Key Features

Doctor-initiated follow-up plans for patients needing extended care.

Medication tracking and progress reports.

Automated notifications to remind patients of medications, check-ins, and appointments.

Two user types:

Tech-Savvy Patients: Interact directly with doctors through the platform.

Non-Tech Patients: A caregiver acts as a bridge, updating progress and communicating on behalf of the patient.

Role-based system: Doctors, patients, and caregivers all have their own dashboards.

Smart expiry system: Follow-up sessions are limited to a set duration (e.g., 1–4 weeks), as determined by the doctor.

AI-powered chat assistant (Phase 1) to handle frequently asked health questions, reducing the doctor’s repetitive workload.

How It Works

1. After discharge, a doctor selects if the patient needs a follow-up plan.

2. If yes, the patient is added to CareLink with a custom follow-up duration.

3. The patient logs in daily (or caregiver does so) to update progress and report issues.

4. Doctor gets notifications and reviews updates, sending advice or new instructions.

5. AI assistant helps answer routine questions immediately.

6. Follow-up automatically ends unless extended by the doctor.

---

Impact

CareLink simplifies and personalizes follow-up care. It:

Increases medication adherence

Reduces avoidable hospital readmissions

Improves doctor-patient relationships

Brings caregivers into the system for non-tech users

Saves time and money for both healthcare providers and patients

Lays the foundation for AI-assisted care in the long term

This is what I'm thinking about the project
This is what ive been thinking lately about the project, not arranged tho, just ideals

We'll build with html,tailwind(css) and little of java, for the authentication we'll have to do it manually (using of breeze is not allowed when building the project)

And for the database, we're not allowed to use PDO

They'll be 4 panels, remember everything/panels will be connected in one way or the other

1. admin: to oversee the platform just as you said, manage users,report and permission (approve caregiver application), I'll add more on this later

2.) Doctor : picked a patient based to prescribe drugs to, see level of improvement on his patient, set next appointment, tweet/post about the patients, chat with patient and lastly assigned caregiver to old patients

Patient: register on the platform, see prescription, next appointment, reminder on when to take next drugs (via the platform,sms,gmail) update the checker box when you take your drugs, see doctors post and Also chat with doctor

Caregiver: sign in or apply as a caregiver, see Patients you're assigned to by the doctor, view and manage patient by giving constant report, checking up on your patient regularly and lastly chat with doctor
