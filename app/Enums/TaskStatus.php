<?php

namespace App\Enums;

enum TaskStatus: String {
  case IN_PROGRESS = 'in_progress';
  case COMPLETED = 'completed';
  case PENDING = 'pending';
}