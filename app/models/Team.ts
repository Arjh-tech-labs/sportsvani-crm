// This file defines the Team model structure
// In a real Laravel application, this would be a Eloquent model

export interface Team {
  id: string // Unique team ID
  name: string
  logo: string // URL to logo image
  captain: string // User ID of captain
  captainName: string
  captainMobile: string
  players: string[] // Array of user IDs
  managers: string[] // Array of user IDs
  createdAt: string
  updatedAt: string
}

export interface TeamStats {
  teamId: string
  matches: number
  won: number
  lost: number
  tied: number
  drawn: number
  winPercentage: number
  tossWon: number
  batFirst: number
  noResult: number
}

export interface TeamPlayer {
  userId: string
  name: string
  role: string // Captain, Vice Captain, Player
  joinedAt: string
}

export interface TeamAward {
  id: string
  name: string
  tournamentId?: string
  date: string
  description?: string
}

