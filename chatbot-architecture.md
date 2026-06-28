# CAUSE-AI Chatbot Architecture

## System Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER INTERFACE                          │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Floating Widget (cause-ai-chatbot.js)                   │  │
│  │  • Purple button (bottom-right)                          │  │
│  │  • Chat window (350px × 500px)                           │  │
│  │  • Message display with typewriter effect                │  │
│  │  • Input