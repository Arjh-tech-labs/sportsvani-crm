import { NextResponse } from "next/server"

// In a real application, this would interact with your database
// This is a simplified example for demonstration purposes

export async function POST(request: Request) {
  try {
    const { matchId, innings, event } = await request.json()

    if (!matchId || !innings || !event) {
      return NextResponse.json({ success: false, message: "Missing required fields" }, { status: 400 })
    }

    // Process the scoring event (in a real app, this would update the database)
    // This could be a run, wicket, boundary, etc.

    // Mock response
    return NextResponse.json({
      success: true,
      message: "Scoring event recorded successfully",
      matchId,
      innings,
      event,
      timestamp: new Date().toISOString(),
    })
  } catch (error) {
    console.error("Error recording scoring event:", error)
    return NextResponse.json(
      { success: false, message: "An error occurred while recording scoring event" },
      { status: 500 },
    )
  }
}

export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url)
    const matchId = searchParams.get("matchId")
    const innings = searchParams.get("innings")

    if (!matchId) {
      return NextResponse.json({ success: false, message: "Match ID is required" }, { status: 400 })
    }

    // Mock data for demonstration
    const scorecard = {
      matchId,
      innings: innings ? Number.parseInt(innings) : 1,
      battingTeam: "TM001",
      battingTeamName: "Mumbai Strikers",
      bowlingTeam: "TM002",
      bowlingTeamName: "Delhi Dragons",
      score: {
        runs: 156,
        wickets: 6,
        overs: 18.4,
        extras: {
          wides: 8,
          noBalls: 2,
          byes: 4,
          legByes: 2,
          penalty: 0,
          total: 16,
        },
      },
      batsmen: [
        {
          playerId: "USR001",
          name: "Amit Kumar",
          runs: 45,
          balls: 38,
          fours: 5,
          sixes: 1,
          strikeRate: 118.42,
          dismissal: "Caught",
          bowlerId: "USR005",
          bowlerName: "Rajesh Singh",
          fielderId: "USR006",
          fielderName: "Vikram Patel",
        },
        {
          playerId: "USR002",
          name: "Rahul Sharma",
          runs: 32,
          balls: 28,
          fours: 3,
          sixes: 0,
          strikeRate: 114.29,
          dismissal: "Bowled",
          bowlerId: "USR007",
          bowlerName: "Sunil Kumar",
        },
      ],
      bowlers: [
        {
          playerId: "USR005",
          name: "Rajesh Singh",
          overs: 4,
          maidens: 0,
          runs: 32,
          wickets: 2,
          economy: 8.0,
          wides: 2,
          noBalls: 1,
        },
        {
          playerId: "USR007",
          name: "Sunil Kumar",
          overs: 3.4,
          maidens: 0,
          runs: 28,
          wickets: 1,
          economy: 7.64,
          wides: 1,
          noBalls: 0,
        },
      ],
      currentBatsmen: ["USR003", "USR004"],
      currentBowler: "USR007",
      lastEvents: [
        {
          over: 18,
          ball: 1,
          type: "Run",
          value: 1,
          batsmanId: "USR003",
          bowlerId: "USR007",
          description: "Single to mid-wicket",
          timestamp: "2025-03-20T15:45:10Z",
        },
        {
          over: 18,
          ball: 2,
          type: "Boundary",
          value: 4,
          batsmanId: "USR004",
          bowlerId: "USR007",
          description: "Four through covers",
          timestamp: "2025-03-20T15:45:40Z",
        },
        {
          over: 18,
          ball: 3,
          type: "Run",
          value: 2,
          batsmanId: "USR004",
          bowlerId: "USR007",
          description: "Two runs to deep square leg",
          timestamp: "2025-03-20T15:46:10Z",
        },
        {
          over: 18,
          ball: 4,
          type: "Wicket",
          value: 0,
          batsmanId: "USR004",
          bowlerId: "USR007",
          description: "Caught at long-on",
          timestamp: "2025-03-20T15:46:40Z",
        },
      ],
    }

    return NextResponse.json({ success: true, scorecard })
  } catch (error) {
    console.error("Error fetching scorecard:", error)
    return NextResponse.json({ success: false, message: "An error occurred while fetching scorecard" }, { status: 500 })
  }
}

