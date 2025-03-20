import { NextResponse } from "next/server"

// In a real application, this would interact with your database
// This is a simplified example for demonstration purposes

export async function GET(request: Request) {
  try {
    // Mock data for demonstration
    const matches = [
      {
        id: "MTH001",
        name: "Mumbai Strikers vs Delhi Dragons",
        matchType: "Limited Overs",
        ballType: "Leather",
        pitchType: "Turf",
        overs: 20,
        powerplayOvers: 6,
        oversPerBowler: 4,
        city: "Mumbai",
        ground: "Mumbai Cricket Ground",
        date: "2025-03-20T14:00:00Z",
        teamA: {
          id: "TM001",
          name: "Mumbai Strikers",
          logo: "/placeholder.svg?height=50&width=50",
        },
        teamB: {
          id: "TM002",
          name: "Delhi Dragons",
          logo: "/placeholder.svg?height=50&width=50",
        },
        tossWinner: "TM001",
        tossDecision: "Bat",
        status: "Scheduled",
        tournamentId: "TRN001",
      },
      {
        id: "MTH002",
        name: "Chennai Challengers vs Bangalore Blasters",
        matchType: "Limited Overs",
        ballType: "Leather",
        pitchType: "Turf",
        overs: 20,
        powerplayOvers: 6,
        oversPerBowler: 4,
        city: "Chennai",
        ground: "Chennai Stadium",
        date: "2025-03-22T14:00:00Z",
        teamA: {
          id: "TM003",
          name: "Chennai Challengers",
          logo: "/placeholder.svg?height=50&width=50",
        },
        teamB: {
          id: "TM004",
          name: "Bangalore Blasters",
          logo: "/placeholder.svg?height=50&width=50",
        },
        tossWinner: "TM004",
        tossDecision: "Bowl",
        status: "Scheduled",
        tournamentId: "TRN001",
      },
    ]

    return NextResponse.json({ success: true, matches })
  } catch (error) {
    console.error("Error fetching matches:", error)
    return NextResponse.json({ success: false, message: "An error occurred while fetching matches" }, { status: 500 })
  }
}

export async function POST(request: Request) {
  try {
    const matchData = await request.json()

    // Validate required fields
    if (!matchData.teamA || !matchData.teamB || !matchData.date || !matchData.overs) {
      return NextResponse.json({ success: false, message: "Missing required fields" }, { status: 400 })
    }

    // Generate a unique match ID (in a real app, this would be handled by the database)
    const matchId = "MTH" + Math.floor(1000 + Math.random() * 9000)

    // Create match name if not provided
    const matchName = matchData.name || `${matchData.teamA.name} vs ${matchData.teamB.name}`

    // Create new match (in a real app, this would be saved to the database)
    const newMatch = {
      id: matchId,
      name: matchName,
      ...matchData,
      createdAt: new Date().toISOString(),
      status: "Scheduled",
    }

    return NextResponse.json({
      success: true,
      message: "Match created successfully",
      match: newMatch,
    })
  } catch (error) {
    console.error("Error creating match:", error)
    return NextResponse.json({ success: false, message: "An error occurred while creating the match" }, { status: 500 })
  }
}

