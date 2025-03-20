import { NextResponse } from "next/server"

// In a real application, this would interact with your database
// This is a simplified example for demonstration purposes

export async function GET(request: Request) {
  try {
    // Mock data for demonstration
    const teams = [
      {
        id: "TM001",
        name: "Mumbai Strikers",
        logo: "/placeholder.svg?height=100&width=100",
        captain: "Amit Kumar",
        captainMobile: "9876543210",
        createdAt: "2023-01-20T11:30:00Z",
        playerCount: 15,
        matchesPlayed: 12,
        matchesWon: 8,
      },
      {
        id: "TM002",
        name: "Delhi Dragons",
        logo: "/placeholder.svg?height=100&width=100",
        captain: "Rahul Sharma",
        captainMobile: "9876543211",
        createdAt: "2023-02-25T14:45:00Z",
        playerCount: 14,
        matchesPlayed: 10,
        matchesWon: 6,
      },
      {
        id: "TM003",
        name: "Chennai Challengers",
        logo: "/placeholder.svg?height=100&width=100",
        captain: "Suresh Raina",
        captainMobile: "9876543212",
        createdAt: "2023-03-15T09:15:00Z",
        playerCount: 16,
        matchesPlayed: 11,
        matchesWon: 7,
      },
    ]

    return NextResponse.json({ success: true, teams })
  } catch (error) {
    console.error("Error fetching teams:", error)
    return NextResponse.json({ success: false, message: "An error occurred while fetching teams" }, { status: 500 })
  }
}

export async function POST(request: Request) {
  try {
    const teamData = await request.json()

    // Validate required fields
    if (!teamData.name || !teamData.captain || !teamData.captainMobile) {
      return NextResponse.json({ success: false, message: "Missing required fields" }, { status: 400 })
    }

    // Generate a unique team ID (in a real app, this would be handled by the database)
    const teamId = "TM" + Math.floor(1000 + Math.random() * 9000)

    // Create new team (in a real app, this would be saved to the database)
    const newTeam = {
      id: teamId,
      ...teamData,
      createdAt: new Date().toISOString(),
      playerCount: 1, // Starting with captain
      matchesPlayed: 0,
      matchesWon: 0,
    }

    return NextResponse.json({
      success: true,
      message: "Team created successfully",
      team: newTeam,
    })
  } catch (error) {
    console.error("Error creating team:", error)
    return NextResponse.json({ success: false, message: "An error occurred while creating the team" }, { status: 500 })
  }
}

