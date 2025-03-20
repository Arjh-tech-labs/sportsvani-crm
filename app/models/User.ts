// This file defines the User model structure
// In a real Laravel application, this would be a Eloquent model

export interface UserRole {
  id: number
  name: string // Player, Umpire, Scorer, Commentator, Organiser, Manager
}

export interface User {
  id: string // Unique user ID
  name: string
  mobile: string
  email?: string
  city: string
  location?: string
  roles: UserRole[] // A user can have multiple roles
  createdAt: string
  updatedAt: string
}

export interface PlayerProfile {
  userId: string
  playerType: "Batter" | "Bowler" | "Allrounder" | "WicketKeeper"
  battingStyle?: "Right" | "Left"
  bowlingStyle?:
    | "Right Arm Fast"
    | "Left Arm Fast"
    | "Right Arm Medium Pacer"
    | "Left Arm Medium Pacer"
    | "Right Arm Off Spin"
    | "Right Arm Leg Spin"
    | "Left Arm Orthodox Spin"
    | "Left Arm Unorthodox Spin"
  stats: PlayerStats
  teams: string[] // Array of team IDs
  matches: string[] // Array of match IDs
  awards: Award[]
  gallery: string[] // Array of image URLs
}

export interface PlayerStats {
  matches: number
  runs: number
  wickets: number
  overs: number
  ballsFaced: number
  average: number
  economy: number
  highestScore: number
  bestBowling: string // e.g., "5/20"
}

export interface Award {
  id: string
  name: string
  matchId?: string // Optional, if award is for a specific match
  tournamentId?: string // Optional, if award is for a tournament
  date: string
  description?: string
}

export interface UmpireProfile {
  userId: string
  matches: number
  tournaments: string[] // Array of tournament IDs
  specialization?: string
}

export interface ScorerProfile {
  userId: string
  matches: number
  tournaments: string[] // Array of tournament IDs
}

