<?php 

namespace App\Services\Bot;

enum UserPoll : string {
    case BasicFeedback = 'basic-feedback';
    case DetailedFeedback = 'detailed-feedback';
}