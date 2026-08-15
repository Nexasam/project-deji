# Real Dashboard Preview and Draft Continuation Design

## Goal

Keep real owner data prominent while showing a clearly labelled sample operations dashboard, and allow an owner to resume editing an existing draft property without creating a duplicate.

## Dashboard

The existing real business/property section remains first. A visually separated sample operational preview follows it in every real-dashboard state, including an empty property portfolio. Sample metrics are explicitly labelled and never presented as business totals.

## Draft continuation

Draft property cards expose a `Continue setup` action. The edit and update endpoints resolve the property through the active business relationship, so a UUID owned by another business returns 404. The current basics form is reused and prefilled; saving updates the same UUID and preserves lifecycle fields, codes, and ownership.

## Scope

This iteration resumes the property-basics stage only. Automatic selection of later incomplete wizard stages will be added when those stages become persistent.

