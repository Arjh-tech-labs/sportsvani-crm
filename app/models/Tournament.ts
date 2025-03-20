// This file defines the Tournament model structure
// In a real Laravel application, this would be a Eloquent model

export interface Tournament {
  id: string // Unique tournament ID
  name: string
  logo: string // URL to logo image
  banner: string // URL to banner image
  organizer: string // User ID of organizer
  organizerName: string
  organizerMobile: string
  organizerEmail: string
  startDate: string
  endDate: string
  category: "Open" | "Corporate" | "Community" | "School" | "College" | "University Series" | "Other"
  ballType: "Leather" | "Tennis" | "Other"
  pitchType: "Rough" | "Cement" | "Turf" | "Matt" | "Other"
  matchType: "Limited Overs" | "Box/Turf" | "Test Match"
  teamCount: number
  fees?: number // Optional, if tournament has fees
  winningPrize?: "Cash" | "Trophy" | "Both" // Optional
  matchDays?: string[] // Array of days, e.g., ['Saturday', 'Sunday']
  matchTimings?: "Day" | "Night" | "Day & Night"
  format: "League" | "Knockout"
  status: "Upcoming" | "Active" | "Completed" | "Cancelled"
  createdAt: string
  updatedAt: string
}

export interface TournamentTeam {
  tournamentId: string
  teamId: string
  teamName: string
  teamLogo: string
  group?: string // Optional, e.g., 'Group A'
  status: "Registered" | "Approved" | "Rejected"
  registeredAt: string
}

export interface TournamentGroup {
  id: string
  tournamentId: string
  name: string // e.g., 'Group A'
  teams: string[] // Array of team IDs
}

export interface TournamentRound {
  id: string
  tournamentId: string
  name: string // e.g., 'League Matches', 'Semi Final', 'Final'
  type: "League" | "Knockout"
  matches: string[] // Array of match IDs
  startDate?: string
  endDate?: string
}

export interface TournamentOfficial {
  tournamentId: string
  userId: string
  name: string
  role: "Umpire" | "Scorer" | "Streamer"
  assignedAt: string
}

export interface TournamentPointsTable {
  tournamentId: string
  groupId?: string // Optional, if points are for a specific group
  teams: TournamentTeamPoints[]
}

export interface TournamentTeamPoints {
  teamId: string
  teamName: string
  played: number
  won: number
  lost: number
  tied: number
  noResult: number
  points: number
  netRunRate: number
}

export interface TournamentLeaderboard {
  tournamentId: string
  mostRuns: LeaderboardPlayer[]
  mostWickets: LeaderboardPlayer[]
  bestBatting: LeaderboardPlayer[]
  bestBowling: LeaderboardPlayer[]
}

export interface LeaderboardPlayer {
  userId: string
  name: string
  teamId: string
  teamName: string
  value: number // Runs, wickets, etc.
  matches: number
}

