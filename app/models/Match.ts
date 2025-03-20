// This file defines the Match model structure
// In a real Laravel application, this would be a Eloquent model

export interface Match {
  id: string // Unique match ID
  name: string
  matchType: "Limited Overs" | "Test"
  ballType: "Leather" | "Tennis" | "Other"
  pitchType: "Rough" | "Matt" | "Cement" | "Turf" | "Other"
  overs: number
  powerplayOvers: number
  oversPerBowler: number
  city: string
  ground: string
  date: string
  teamA: {
    id: string
    name: string
    logo: string
    squad?: string[] // Array of player IDs
    captain?: string // Player ID
    wicketkeeper?: string // Player ID
  }
  teamB: {
    id: string
    name: string
    logo: string
    squad?: string[] // Array of player IDs
    captain?: string // Player ID
    wicketkeeper?: string // Player ID
  }
  tossWinner?: string // Team ID
  tossDecision?: "Bat" | "Bowl"
  officials: {
    umpires: string[] // Array of user IDs
    scorers: string[] // Array of user IDs
    commentators?: string[] // Array of user IDs
    streamers?: string[] // Array of user IDs
  }
  tournamentId?: string // Optional, if match is part of a tournament
  roundId?: string // Optional, if match is part of a tournament round
  status: "Scheduled" | "Live" | "Completed" | "Abandoned" | "Cancelled"
  result?: MatchResult
  createdAt: string
  updatedAt: string
}

export interface MatchResult {
  winner?: string // Team ID
  winnerName?: string
  winMargin?: number
  winMarginType?: "Runs" | "Wickets"
  manOfTheMatch?: string // Player ID
  manOfTheMatchName?: string
  teamAScore?: Innings
  teamBScore?: Innings
  summary?: string
}

export interface Innings {
  runs: number
  wickets: number
  overs: number
  extras: {
    wides: number
    noBalls: number
    byes: number
    legByes: number
    penalty: number
    total: number
  }
}

export interface BattingScorecard {
  matchId: string
  teamId: string
  innings: number
  batsmen: BatsmanScore[]
}

export interface BatsmanScore {
  playerId: string
  name: string
  runs: number
  balls: number
  fours: number
  sixes: number
  strikeRate: number
  dismissal?: string
  bowlerId?: string
  bowlerName?: string
  fielderId?: string
  fielderName?: string
}

export interface BowlingScorecard {
  matchId: string
  teamId: string
  innings: number
  bowlers: BowlerScore[]
}

export interface BowlerScore {
  playerId: string
  name: string
  overs: number
  maidens: number
  runs: number
  wickets: number
  economy: number
  wides: number
  noBalls: number
}

export interface MatchEvent {
  matchId: string
  innings: number
  over: number
  ball: number
  type: "Run" | "Boundary" | "Six" | "Wicket" | "Wide" | "NoBall" | "Bye" | "LegBye" | "Penalty"
  value: number
  batsmanId: string
  bowlerId: string
  description?: string
  timestamp: string
}

export interface WagonWheel {
  matchId: string
  batsmanId: string
  shots: Shot[]
}

export interface Shot {
  over: number
  ball: number
  runs: number
  x: number // X coordinate on the field (0-100)
  y: number // Y coordinate on the field (0-100)
  angle: number // Angle of the shot (0-360)
  distance: number // Distance of the shot
  shotType?: string // e.g., 'Cover Drive', 'Pull', etc.
}

