import { NextResponse } from "next/server"

// In a real application, this would interact with your database
// This is a simplified example for demonstration purposes

export async function GET(request: Request) {
  try {
    // Mock data for demonstration
    const tournaments = [
      {
        id: "TRN001",
        name: "Corporate Cricket League 2025",
        logo: "/placeholder.svg?height=100&width=100",
        banner: "/placeholder.svg?height=300&width=800",
        organizer: "Amit Kumar",
        organizerMobile: "9876543210",
        organizerEmail: "amit@example.com",
        startDate: "2025-03-15T00:00:00Z",
        endDate: "2025-04-15T00:00:00Z",
        category: "Corporate",
        ballType: "Leather",
        pitchType: "Turf",
        matchType: "Limited Overs",
        teamCount: 12,
        status: "Active",
      },
      {
        id: "TRN002",
        name: "Mumbai Community Cup",
        logo: "/placeholder.svg?height=100&width=100",
        banner: "/placeholder.svg?height=300&width=800",
        organizer: "Rahul Sharma",
        organizerMobile: "9876543211",
        organizerEmail: "rahul@example.com",
        startDate: "2025-05-01T00:00:00Z",
        endDate: "2025-05-30T00:00:00Z",
        category: "Community",
        ballType: "Tennis",
        pitchType: "Cement",
        matchType: "Limited Overs",
        teamCount: 8,
        status: "Upcoming",
      },
      {
        id: "TRN003",
        name: "School Cricket Championship",
        logo: "/placeholder.svg?height=100&width=100",
        banner: "/placeholder.svg?height=300&width=800",
        organizer: "Priya Singh",
        organizerMobile: "9876543212",
        organizerEmail: "priya@example.com",
        startDate: "2025-07-10T00:00:00Z",
        endDate: "2025-08-10T00:00:00Z",
        category: "School",
        ballType: "Leather",
        pitchType: "Matt",
        matchType: "Limited Overs",
        teamCount: 16,
        status: "Upcoming",
      },
    ]

    return NextResponse.json({ success: true, tournaments })
  } catch (error) {
    console.error("Error fetching tournaments:", error)
    return NextResponse.json(
      { success: false, message: "An error occurred while fetching tournaments" },
      { status: 500 },
    )
  }
}

export async function POST(request: Request) {
  try {
    const tournamentData = await request.json()

    // Validate required fields
    if (!tournamentData.name || !tournamentData.organizer || !tournamentData.startDate || !tournamentData.endDate) {
      return NextResponse.json({ success: false, message: "Missing required fields" }, { status: 400 })
    }

    // Generate a unique tournament ID (in a real app, this would be handled by the database)
    const tournamentId = "TRN" + Math.floor(1000 + Math.random() * 9000)

    // Create new tournament (in a real app, this would be saved to the database)
    const newTournament = {
      id: tournamentId,
      ...tournamentData,
      createdAt: new Date().toISOString(),
      status: "Upcoming",
    }

    return NextResponse.json({
      success: true,
      message: "Tournament created successfully",
      tournament: newTournament,
    })
  } catch (error) {
    console.error("Error creating tournament:", error)
    return NextResponse.json(
      { success: false, message: "An error occurred while creating the tournament" },
      { status: 500 },
    )
  }
}

