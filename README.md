# University Classroom Management System v2.0

## Overview

This project is a modern, scalable, and AI-driven evolution of the original "University Classroom Management System" (2018). It has been re-architected from a monolithic structure into a **Service-Oriented Architecture (SOA)** to handle high concurrency, automated scheduling, and intelligent task management.

## Key Features

- **High Concurrency & Integrity:** Implementation of Pessimistic Locking in PostgreSQL to prevent double-booking race conditions.
- **AI Automated Scheduling:** A constraint-satisfaction engine built with Python and Google OR-Tools to generate conflict-free timetables.
- **Task-Oriented AI Agent:** An intelligent assistant using LLM function calling to query availability and execute bookings via API.
- **Cross-Platform Access:** Modern Web Dashboard (React) and Native Mobile Application (React Native).

## Tech Stack

- **Core API:** Laravel 11 (PHP 8.3)
- **AI & Optimization Service:** FastAPI (Python 3.12)
- **Frontend:** React 18 (Vite, Tailwind CSS)
- **Mobile:** React Native (Expo)
- **Infrastructure:** Docker, Nginx Reverse Proxy, Redis
- **Database:** PostgreSQL 16

## Project Structure

```text
├── api/             # Laravel 11 Core API
├── ai/              # Python FastAPI & AI Logic
├── web/             # React.js Web Application
├── mobile/          # React Native Mobile App
├── docker/          # Nginx & Infrastructure configurations
└── docker-compose.yml
```

## Development Setup

The entire environment is containerized. To start the system:

```bash
docker-compose up -d
```

## Academic Context

This project is a Bachelor's Thesis at the **University of Peloponnese**, Department of Informatics and Telecommunications. It focuses on modernizing legacy enterprise systems using cloud-native technologies and applied Artificial Intelligence.

**Author:** Athanasios Davaris

**Date:** April 2026
