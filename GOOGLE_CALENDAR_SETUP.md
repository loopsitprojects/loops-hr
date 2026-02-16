# Google Calendar OAuth2 Setup Instructions

## Step 1: Create OAuth2 Credentials in Google Cloud Console

1. Go to [Google Cloud Console](https://console.cloud.google.com)
2. Select or create a project
3. Enable **Google Calendar API**:
   - Navigate to "APIs & Services" → "Library"
   - Search for "Google Calendar API"
   - Click "Enable"

4. Create **OAuth 2.0 Client ID**:
   - Go to "APIs & Services" → "Credentials"
   - Click "Create Credentials" → "OAuth client ID"
   - If prompted, configure the OAuth consent screen:
     - User Type: External (or Internal if using Google Workspace)
     - App name: "LoopsHR" (or your app name)
     - User support email: oshanf92@gmail.com
     - Developer contact: oshanf92@gmail.com
     - Add scope: `https://www.googleapis.com/auth/calendar`
   - Application type: **Web application**
   - Name: "LoopsHR Calendar Integration"
   - Authorized redirect URIs: 
     - `http://127.0.0.1:8000/google/calendar/callback`
     - `http://localhost:8000/google/calendar/callback`
   - Click "Create"

5. **Download the credentials**:
   - Click the download icon next to your newly created OAuth client
   - Save the JSON file as `google-oauth-credentials.json`
   - Place it in: `storage/app/google-oauth-credentials.json`

## Step 2: Update .env File

Add these lines to your `.env` file (optional, for reference):

```env
# Google Calendar OAuth (credentials are in storage/app/google-oauth-credentials.json)
GOOGLE_CALENDAR_ENABLED=true
```

## Step 3: Authorize the Application

1. Open your browser and visit:
   ```
   http://127.0.0.1:8000/google/calendar/redirect
   ```

2. Sign in with your Google account: **oshanf92@gmail.com**

3. Grant the following permissions:
   - View and edit events on all your calendars
   - See and download any calendar you can access using your Calendar

4. You'll be redirected back to the dashboard with a success message

## Step 4: Test Interview Scheduling

1. Go to a candidate in the recruitment module
2. Click the schedule button (calendar icon)
3. Fill in the interview details:
   - Date & Time
   - Duration
   - Select an HOD
   - Add additional guests (optional)
4. Click "Schedule & Send Invite"

**Expected Results:**
✅ Google Calendar event created in oshanf92@gmail.com's calendar
✅ Google Meet link generated automatically
✅ Email sent to candidate, HOD, and additional guests
✅ Interview record saved in database

## Troubleshooting

### "OAuth credentials file not found"
- Make sure `google-oauth-credentials.json` is in `storage/app/`
- Check file permissions

### "Authorization failed"
- Make sure the redirect URI in Google Cloud Console matches exactly
- Clear browser cookies and try again

### "Token expired"
- The system automatically refreshes tokens
- If issues persist, re-authorize by visiting `/google/calendar/redirect`

## File Locations

- OAuth Credentials: `storage/app/google-oauth-credentials.json`
- Access Token: `storage/app/google-calendar-token.json` (auto-generated)

## How to Switch Accounts (e.g. to a real HR Manager)

When you are ready to switch the account from your developer account to the real HR manager's account:

1. **Clear Existing Token**: 
   - Delete the file `storage/app/google-calendar-token.json`.
   
2. **Re-authorize**:
   - Visit `http://127.0.0.1:8000/google/calendar/redirect`.
   - Sign in with the **new** HR manager account.
   - Grant permissions.

3. **(Optional) Update Credentials**:
   - If the HR manager belongs to a different Google Cloud Project, replace `storage/app/google-oauth-credentials.json` with their project's JSON.
   - Most of the time, you can keep the same credentials file and just sign in with a different user.


## Step 5: How HODs Share Their Calendar

For the Availability Check to work, each HOD must share their calendar with the HR account (e.g., `oshanf92@gmail.com`).

**Instructions for HODs:**

1.  Open **Google Calendar** (calendar.google.com).
2.  On the left sidebar, find "My calendars".
3.  Hover over your calendar and click the **three dots** (Options).
4.  Click **"Settings and sharing"**.
5.  Scroll down to **"Share with specific people"**.
6.  Click **"+ Add people"**.
7.  Enter the HR email address (e.g., `oshanf92@gmail.com`).
8.  Under "Permissions", select **"See all event details"** (or at least "See only free/busy").
9.  Click **"Send"**.

Once shared, the HR system will be able to see "Occupied" slots for that HOD in the scheduling modal.
