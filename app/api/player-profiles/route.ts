import { NextResponse } from "next/server"

// In a real application, this would interact with your database
// This is a simplified example for demonstration purposes

export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url)
    const userId = searchParams.get("userId")

    if (!userId) {
      return NextResponse.json({ success: false, message: "User ID is required" }, { status: 400 })
    }

    // Mock data for demonstration
    const playerProfile = {
      userId: userId,
      playerType: "Allrounder",
      battingStyle: "Right",
      bowlingStyle: "Right Arm Medium Pacer",
      stats: {
        matches: 45,
        runs: 1250,
        wickets: 35,
        overs: 120,
        ballsFaced: 1500,
        average: 27.8,
        economy: 6.2,
        highestScore: 87,
        bestBowling: "4/22",
      },
      teams: ["TM001", "TM005"],
      matches: ["MTH001", "MTH003", "MTH007"],
      awards: [
        {
          id: "AWD001",
          name: "Player of the Match",
          matchId: "MTH003",
          date: "2024-12-15T00:00:00Z",
          description: "For scoring 87 runs and taking 3 wickets",
        },
      ],
      gallery: ["/placeholder.svg?height=300&width=400", "/placeholder.svg?height=300&width=400"],
    }

    return NextResponse.json({ success: true, playerProfile })
  } catch (error) {
    console.error("Error fetching player profile:", error)
    return NextResponse.json(
      { success: false, message: "An error occurred while fetching player profile" },
      { status: 500 },
    )
  }
}

export async function POST(request: Request) {
  try {
    const profileData = await request.json()

    // Validate required fields
    if (!profileData.userId || !profileData.playerType) {
      return NextResponse.json({ success: false, message: "Missing required fields" }, { status: 400 })
    }

    // Create new player profile (in a real app, this would be saved to the database)
    const newProfile = {
      ...profileData,
      stats: profileData.stats || {
        matches: 0,
        runs: 0,
        wickets: 0,
        overs: 0,
        ballsFaced: 0,
        average: 0,
        economy: 0,
        highestScore: 0,
        bestBowling: "0/0",
      },
      teams: profileData.teams || [],
      matches: profileData.matches || [],
      awards: profileData.awards || [],
      gallery: profileData.gallery || [],
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    }

    return NextResponse.json({
      success: true,
      message: "Player profile created successfully",
      playerProfile: newProfile,
    })
  } catch (error) {
    console.error("Error creating player profile:", error)
    return NextResponse.json(
      { success: false, message: "An error occurred while creating player profile" },
      { status: 500 },
    )
  }
}

export async function PUT(request: Request) {
  try {
    const profileData = await request.json()

    // Validate required fields
    if (!profileData.userId) {
      return NextResponse.json({ success: false, message: "User ID is required" }, { status: 400 })
    }

    // Update player profile (in a real app, this would update the database)
    const updatedProfile = {
      ...profileData,
      updatedAt: new Date().toISOString(),
    }

    return NextResponse.json({
      success: true,
      message: "Player profile updated successfully",
      playerProfile: updatedProfile,
    })
  } catch (error) {
    console.error("Error updating player profile:", error)
    return NextResponse.json(
      { success: false, message: "An error occurred while updating player profile" },
      { status: 500 },
    )
  }
}

