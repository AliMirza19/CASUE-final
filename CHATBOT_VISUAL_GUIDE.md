# CAUSE-AI Chatbot Visual Guide

## User Interface Components

### 1. Floating Button (Closed State)
```
┌─────────────────────────────────────┐
│                                     │
│                                     │
│                                     │
│                                     │
│                                     │
│                                     │
│                                     │
│                                     │
│                                     │
│                                     │
│                                 ┌───┐
│                                 │ 💬│ ← Purple circular button
│                                 └───┘
└─────────────────────────────────────┘
```

### 2. Chat Window (Open State)
```
┌─────────────────────────────────────┐
│                                     │
│                                     │
│                    ┌────────────────┤
│                    │ CAUSE-AI    ✕ │ ← Header (Purple gradient)
│                    │ Virtual Asst   │
│                    ├────────────────┤
│                    │                │
│                    │ 👋 Hi! I'm     │ ← Welcome message
│                    │ CAUSE-AI...    │
│                    │                │
│                    │     [User msg] │ ← User messages (right)
│                    │                │
│                    │ [AI response]  │ ← AI messages (left)
│                    │                │
│                    ├────────────────┤
│                    │ [Type here...] │ ← Input field
│                    │            [➤] │ ← Send button
│                    └────────────────┘
└─────────────────────────────────────┘
```

## Color Scheme
- **Primary**: Purple (#7C3AED / purple-600)
- **Hover**: Dark Purple (#5B21B6 / purple-700)
- **Background**: White (#FFFFFF)
- **User Messages**: Purple background, white text
- **AI Messages**: White background, gray text with shadow
- **Input Border**: Gray (#D1D5DB / gray-300)
- **Focus Ring**: Purple (#7C3AED / purple-500)

## Animations

### Typewriter Effect
```
AI: H
AI: He
AI: Hel
AI: Hell
AI: Hello
AI: Hello!
AI: Hello! H
AI: Hello! Ho
AI: Hello! How
AI: Hello! How c
AI: Hello! How ca
AI: Hello! How can
AI: Hello! How can I
AI: Hello! How can I h
AI: Hello! How can I he
AI: Hello! How can I hel
AI: Hello! How can I help
AI: Hello! How can I help?
```
Speed: 20ms per character

### Loading Indicator
```
● ○ ○  (bounce)
○ ● ○  (bounce)
○ ○ ●  (bounce)
```
Three gray dots with staggered bounce animation

## Responsive Behavior

### Desktop (>768px)
- Widget: 384px width × 500px height
- Position: Fixed bottom-right (24px from edges)
- Button: 64px × 64px

### Mobile (<768px)
- Widget: Full width - 32px margin
- Position: Fixed bottom-right (16px from edges)
- Button: 56px × 56px

## User Interactions

### Opening Chat
1. User clicks purple button
2. Button stays visible
3. Chat window slides up from bottom
4. Previous 5 conversations load
5. Input field auto-focuses

### Sending Message
1. User types message
2. User presses Enter or clicks Send
3. Message appears on right (purple bubble)
4. Loading dots appear on left
5. AI response types out character by character
6. Response appears on left (white bubble)

### Closing Chat
1. User clicks X button in header
2. Chat window slides down
3. Purple button remains visible
4. Chat history preserved

## Message Format

### User Message
```
┌─────────────────────────────────┐
│                                 │
│              ┌──────────────────┤
│              │ How do I create  │
│              │ an event budget? │
│              └──────────────────┘
│                                 │
└─────────────────────────────────┘
```
- Alignment: Right
- Background: Purple (#7C3AED)
- Text: White
- Max width: 80%
- Padding: 16px horizontal, 8px vertical
- Border radius: 8px

### AI Message
```
┌─────────────────────────────────┐
│                                 │
│ ┌──────────────────────────────┐│
│ │ To create an event budget,   ││
│ │ you need to itemize each     ││
│ │ expense with Rate × Quantity ││
│ └──────────────────────────────┘│
│                                 │
└─────────────────────────────────┘
```
- Alignment: Left
- Background: White
- Text: Gray (#1F2937)
- Shadow: Medium
- Max width: 80%
- Padding: 16px horizontal, 8px vertical
- Border radius: 8px

## Accessibility Features
- ✅ Keyboard navigation (Tab, Enter)
- ✅ Focus indicators (purple ring)
- ✅ ARIA labels on buttons
- ✅ High contrast text
- ✅ Readable font sizes (14px-16px)
- ✅ Clear visual hierarchy

## Browser Compatibility
- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## Performance
- Initial load: <100ms
- Message send: ~2-5 seconds (API dependent)
- Typewriter effect: ~1-2 seconds per response
- History load: <500ms
- Smooth 60fps animations
