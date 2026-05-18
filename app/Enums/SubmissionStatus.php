<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Draft = 'draft';
    case SubmittedPendingSupervisor = 'submitted_pending_supervisor';
    case SupervisorApproved = 'supervisor_approved';
    case SupervisorRevisionRequested = 'supervisor_revision_requested';
    case SubmittedBySupervisor = 'submitted_by_supervisor';
    case WithHod = 'with_hod';
    case UnderInternalEval = 'under_internal_eval';
    case WithFpgcR = 'with_fpgc_r';
    case WithHdc = 'with_hdc';
    case Approved = 'approved';
    case RevisionsRequired = 'revisions_required';
    case Rejected = 'rejected';
    case Withdrawn = 'withdrawn';
}
