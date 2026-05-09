<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Draft = 'draft';
    case SubmittedPendingSupervisor = 'submitted_pending_supervisor';
    case SupervisorApproved = 'supervisor_approved';
    case SupervisorRevisionRequested = 'supervisor_revision_requested';
    case Rejected = 'rejected';
}
