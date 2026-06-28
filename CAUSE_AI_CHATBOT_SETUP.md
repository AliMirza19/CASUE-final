# CAUSE-AI Chatbot Implementation Guide

## Overview
CAUSE-AI is a virtual assistant chatbot integrated into the CAUSE Society Management System, powered by Google's Gemini AI. It provides contextual assistance to HOD, Patron, and President roles.

## Features
- ✅ Role-aware AI responses (HOD, Patron, President)
- ✅ Persistent chat history stored in database
- ✅ Typewriter effect for AI responses
- ✅ Exponential backoff retry mechanism (5 retries)
- ✅ Floating widget UI with Tailwind CSS
- ✅ Loading indicators and error handling
- ✅ Previous conversation history display

## Installation Steps

### 1. Get Gemini API Key
1. Visit [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Sign in with your Google account
3. Click "Create API Key"
4. Copy the generated API key

### 2. Configure Environment
Add to your `.env` file:
```env
GEMINI_API_KEY=your_actual_api_key_here
```

### 3. Run Database Migration
```bash
php artisan migrate
```

This creates the `chat_histories` table with:
- `id` - Primary key
- `user_id` - Foreign key to users table
- `message` - User's message (text)
- `response` - AI's response (text)
- `role_context` - User's role (HOD/Patron/President)
- `timestamps` - Created/updated timestamps

### 4. Build Frontend Assets
```bash
npm install
npm run build
```

Or for development:
```bash
npm run dev
```

## Usage

### For End Users
1. Log in as HOD, Patron, or President
2. Look for the purple chat icon in the bottom-right corner
3. Click to open the chat window
4. Type your question and press Enter or click Send
5. View previous conversations automatically loaded

### Example Queries
- **President**: "Help me write an event description for a tech workshop"
- **HOD**: "What's the recommended budget allocation for academic events?"
- **Patron**: "How should I review this event budget?"

## Technical Details

### API Endpoint
- **POST** `/api/ai-chat` - Send message to AI
- **GET** `/api/ai-chat/history` - Retrieve chat history

### Request Format
```json
{
  "message": "Your question here",
  "role": "HOD"
}
```

### Response Format
```json
{
  "success": true,
  "response": "AI response text"
}
```

### System Instruction
The AI is configured with context about:
- Event submission workflow (Student → President → Patron → HOD)
- Budget itemization rules (Rate × Quantity)
- Role-specific permissions (e.g., only HOD sees financial charts)
- Society management guidelines

### Error Handling
- Network errors: Displays user-friendly message
- API failures: Implements exponential backoff (1s, 2s, 4s, 8s, 16s)
- Timeout: 30 seconds per request
- Validation: Max 1000 characters per message

## File Structure
```
app/
├── Http/Controllers/
│   └── ChatController.php          # Backend logic
├── Models/
│   └── ChatHistory.php             # Database model
database/
└── migrations/
    └── 2026_04_15_000001_create_chat_histories_table.php
resources/
├── js/
│   └── cause-ai-chatbot.js         # Frontend widget
└── views/
    └── layouts/
        └── app.blade.php            # Layout integration
routes/
└── web.php                          # API routes
```

## Customization

### Change AI Model
Edit `ChatController.php`:
```php
private const MODEL = 'gemini-2.5-flash-preview-09-2025';
```

### Modify System Instruction
Edit the `getSystemInstruction()` method in `ChatController.php`

### Adjust Widget Position
Edit `cause-ai-chatbot.js`:
```javascript
// Change from bottom-right to bottom-left
class="fixed bottom-6 left-6 ..."
```

### Change Theme Color
Edit the widget HTML in `cause-ai-chatbot.js`:
```javascript
// Replace purple-600 with your color
bg-purple-600 → bg-blue-600
```

## Troubleshooting

### Chatbot not appearing
- Check if user role is HOD, Patron, or President
- Verify `npm run build` completed successfully
- Clear browser cache

### API errors
- Verify `GEMINI_API_KEY` is set in `.env`
- Check API key is valid at Google AI Studio
- Review `storage/logs/laravel.log` for errors

### Database errors
- Run `php artisan migrate` again
- Check database connection in `.env`

### CSRF token errors
- Ensure `<meta name="csrf-token">` is in layout
- Clear application cache: `php artisan cache:clear`

## Security Notes
- API key stored in `.env` (never commit to git)
- CSRF protection enabled on all routes
- User authentication required
- Role-based access control
- Input validation (max 1000 chars)
- SQL injection protection via Eloquent ORM

## Performance
- Chat history limited to last 50 messages
- Only last 5 conversations displayed on load
- 30-second timeout per API call
- Exponential backoff prevents API spam

## Future Enhancements
- [ ] Add file upload support
- [ ] Implement conversation threading
- [ ] Add voice input/output
- [ ] Multi-language support
- [ ] Export chat history as PDF
- [ ] Admin dashboard for monitoring usage

## Support
For issues or questions, contact the development team or refer to:
- [Gemini API Documentation](https://ai.google.dev/docs)
- [Laravel Documentation](https://laravel.com/docs)
