# 🧠 CAUSE AI Cognitive Core: User Testing & Feature Guide

Welcome to the heart of the CAUSE Society Management System. All AI features are now powered by the **Groq Cognitive Core (Llama 3.3)** for ultra-fast, professional intelligence.

---

## 1. AI Smart Ticker (Proactive Monitoring)
**Benefit:** Gives leadership a "birds-eye view" of society health without reading reports.
- **How it works:** `SmartTickerService` analyzes active budget usage, pending reviews, and election status every 30 minutes to generate real-time insights.
- **How to Test:**
    1. Log in as **Patron**, **HOD**, or **Student Affairs**.
    2. Look at the moving marquee ticker at the top of the dashboard.
    3. You should see insights like: *"Budget utilization is at 15%. Optimization recommended,"* or *"Registration is LIVE! 5 candidates approved."*

## 2. AI Risk Auditor (Event Proposals)
**Benefit:** Prevents corruption and logistical disasters before they happen.
- **How it works:** When a student submits an event, `AiRiskService` audits the itemized costs against market norms and flags logistical anomalies.
- **How to Test:**
    1. Log in as **President** or **HOD**.
    2. Go to **Manage Events** -> **Review**.
    3. Look for the "AI Risk Assessment" card. If a student requested Rs. 5,000 for a pen, AI will flag it as **High Risk** with an anomaly alert.

## 3. AI Volunteer Matching (Smart Recruitment)
**Benefit:** Matches the right skills to the right tasks, ensuring event success.
- **How it works:** `AiGovernanceService` compares student skills (from their profiles) with the event's description and ranks them by suitability scores (0-100%).
- **How to Test:**
    1. Log in as **Volunteer Coordinator**.
    2. Go to **Volunteer Requests** and view a request.
    3. Click the **"AI Suggestions"** button. AI will rank students and explain why (e.g., *"Selected due to Graphic Design experience"*).

## 4. AI Manifesto Optimizer (Election Growth)
**Benefit:** Helps students sound professional and inspires the student body.
- **How it works:** During candidate registration, AI analyzes the student's draft manifesto and suggests a more structured, professional version.
- **How to Test:**
    1. Log in as a **Student** during an active election registration period.
    2. Go to **Election** -> **Register as Candidate**.
    3. Type a rough manifesto (e.g., *"I will help everyone"*) and click **"AI Optimize"**. AI will rewrite it into a professional growth-focused manifesto.

## 5. AI Creative Assistant (Media Workflow)
**Benefit:** Sets a high standard for society branding and social media presence.
- **How it works:** `AiCreativeEngineService` suggests "Visual Themes" (Minimalist, Cyberpunk, etc.) and generates social media captions for events.
- **How to Test:**
    1. Log in as **Graphic Designer**.
    2. Go to **Pending Designs**.
    3. Open a task. You will see a section: **"AI Creative Persona"**. AI will suggest a theme like *"Corporate Professional: Use Navy Blue and White to reflect the seminar's importance."*

## 6. AI Decision Support (Leadership Insights)
**Benefit:** Protects the budget and ensures "Market Rate Verification."
- **How it works:** `AiDecisionSupportService` audits individual budget items and suggests whether the HOD should approve, reduce, or reject costs.
- **How to Test:**
    1. Log in as **HOD**.
    2. Review an event budget.
    3. AI will display a note: *"Predictive Budgeting: This refreshment cost is 15% higher than average. Historical tech-week events spent Rs. 4,000 less."*

## 7. AI Meeting Minutes (Action Items)
**Benefit:** Never forget a decision made in a chat.
- **How it works:** `AiMeetingMinutesService` reads the last 50 messages between HOD and Patron to extract a bulleted list of "Action Items."
- **How to Test:**
    1. Log in as **HOD** or **Patron**.
    2. Go to **Chat** with each other and send 5-10 messages about an event (e.g., *"Add Rs. 500 for certificates"*, *"Change date to Monday"*).
    3. Click **"Generate AI Minutes"**. AI will list the key decisions made.

---

### 🚀 Performance Tip:
All these features use **Groq's Llama 3.3**, so responses should be almost instantaneous. Ensure your `.env` file has the `GROQ_API_KEY` set.
