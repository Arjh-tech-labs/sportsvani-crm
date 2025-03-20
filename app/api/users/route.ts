import { NextResponse } from "next/server"

// In a real application, this would interact with your database
// This is a simplified example for demonstration purposes

export async function GET(request: Request) {
  try {
    // Mock data for demonstration
    const users = [
      {
        id: "USR001",
        name: "Amit Kumar",
        mobile: "9876543210",
        roles: ["Player", "Umpire", "Scorer"],
        city: "Mumbai",
        createdAt: "2023-01-15T10:30:00Z",
      },
      {
        id: "USR002",
        name: "Rahul Sharma",
        mobile: "9876543211",
        roles: ["Player", "Manager"],
        city: "Delhi",
        createdAt: "2023-02-20T14:45:00Z",
      },
      {
        id: "USR003",
        name: "Priya Singh",
        mobile: "9876543212",
        roles: ["Organiser", "Manager"],
        city: "Bangalore",
        createdAt: "2023-03-10T09:15:00Z",
      },
    ]

    return NextResponse.json({ success: true, users })
  } catch (error) {
    console.error("Error fetching users:", error)
    return NextResponse.json({ success: false, message: "An error occurred while fetching users" }, { status: 500 })
  }
}

export async function POST(request: Request) {
  try {
    const userData = await request.json()

    // Validate required fields
    if (!userData.name || !userData.mobile || !userData.roles || userData.roles.length === 0) {
      return NextResponse.json({ success: false, message: "Missing required fields" }, { status: 400 })
    }

    // Generate a unique user ID (in a real app, this would be handled by the database)
    const userId = "USR" + Math.floor(1000 + Math.random() * 9000)

    // Create new user (in a real app, this would be saved to the database)
    const newUser = {
      id: userId,
      ...userData,
      createdAt: new Date().toISOString(),
    }

    return NextResponse.json({
      success: true,
      message: "User created successfully",
      user: newUser,
    })
  } catch (error) {
    console.error("Error creating user:", error)
    return NextResponse.json({ success: false, message: "An error occurred while creating the user" }, { status: 500 })
  }
}

