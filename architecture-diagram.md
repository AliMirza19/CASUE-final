# Software Architecture Diagram - Cause Society Management System

## Simplified Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                        PRESENTATION LAYER                        │
├─────────────────────────────────────────────────────────────────┤
│  Web Interface (Blade Templates)                               │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐              │
│  │   Student   │ │   Faculty   │ │    Admin    │              │
│  │ Dashboard   │ │ Dashboard   │ │ Dashboard   │              │
│  └─────────────┘ └─────────────┘ └─────────────┘              │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐              │
│  │     HOD     │ │   Patron    │ │ President   │              │
│  │ Dashboard   │ │ Dashboard   │ │ Dashboard   │              │
│  └─────────────┘ └─────────────┘ └─────────────┘              │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                      APPLICATION LAYER                          │
├─────────────────────────────────────────────────────────────────┤
│  Controllers & Middleware                                       │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐              │
│  │    Auth     │ │   Events    │ │   Admin     │              │
│  │Controllers  │ │Controllers  │ │Controllers  │              │
│  └─────────────┘ └─────────────┘ └─────────────┘              │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │              Middleware Layer                           │   │
│  │  • Authentication • Authorization • Role-based Access  │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                       BUSINESS LAYER                            │
├─────────────────────────────────────────────────────────────────┤
│  Services & Business Logic                                      │
│  ┌─────────────────┐ ┌─────────────────┐                      │
│  │   Event         │ │   Financial     │                      │
│  │ Workflow        │ │  Analytics      │                      │
│  │  Service        │ │   Service       │                      │
│  └─────────────────┘ └─────────────────┘                      │
│                                                                 │
│  Business Rules:                                                │
│  • Event Approval Workflow                                     │
│  • Budget Management                                            │
│  • Role-based Permissions                                      │
│  • Election Management                                          │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                        DATA LAYER                               │
├─────────────────────────────────────────────────────────────────┤
│  Models & Database                                              │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐              │
│  │    User     │ │    Event    │ │   Budget    │              │
│  │   Model     │ │   Model     │ │   Model     │              │
│  └─────────────┘ └─────────────┘ └─────────────┘              │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐              │
│  │ Candidate   │ │    Vote     │ │  Activity   │              │
│  │   Model     │ │   Model     │ │    Log      │              │
│  └─────────────┘ └─────────────┘ └─────────────┘              │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                MySQL Database                           │   │
│  │  Tables: users, events, budgets, votes, candidates     │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

## Key Components

### 1. User Roles & Access
- **Students**: Create events, view status
- **Faculty**: Review and approve events
- **HOD**: Budget management, analytics
- **Patron**: Final event approval
- **Admin**: System management
- **President**: Strategic oversight

### 2. Core Workflows
- **Event Management**: Creation → Review → Approval → Execution
- **Budget Tracking**: Allocation → Monitoring → Reporting
- **Election System**: Candidate registration → Voting → Results

### 3. Data Flow
```
User Request → Controller → Middleware → Service → Model → Database
                    ↓
              Response ← View ← Controller ← Service ← Model
```

## Tools for Visual Diagrams

To create actual visual diagrams, you can use:

1. **Online Tools**:
   - Draw.io (diagrams.net)
   - Lucidchart
   - Miro
   - Figma

2. **Code-based Diagrams**:
   - Mermaid (GitHub supported)
   - PlantUML
   - Graphviz

3. **AI-powered Tools**:
   - Excalidraw
   - Whimsical
   - Creately

Would you like me to create a Mermaid diagram version that can be rendered visually in GitHub or other platforms?